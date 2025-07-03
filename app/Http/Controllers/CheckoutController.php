<?php

namespace App\Http\Controllers;

use App\Models\ExchangeRate;
use App\Models\Factura;
use App\Models\Product;
use App\Models\Promotion;
use App\Models\Ticket;
use App\Models\TicketItem;
use App\Models\Venta; // Import the Venta model
use App\Services\BcvService;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Exception;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        $currentExchangeRate = ExchangeRate::current();
        $bcvRate = (float) ($currentExchangeRate->rate ?? 0);

        $user = $request->user();
        $cartItems = session()->get('cartItems', []);

        if (empty($cartItems)) {
            // Optional: Handle empty cart, e.g., redirect
        }

        return Inertia::render('Checkout/Checkout', [
            'cartItems' => $cartItems,
            'user' => $user ? $user->toArray() : null,
            'errors' => session('errors') ? session('errors')->getBag('default')->getMessages() : [],
            'bcvRate' => $bcvRate, // Esta es la tasa obtenida manualmente
        ]);
    }

    public function store(Request $request)
    {
        // Inicializar $validatedData al principio para satisfacer a Intelephense (buena práctica)
        $validatedData = [];
        $finalAmount = 0; // Inicializar también finalAmount

        try {
            if (empty($request->input('items'))) {
                throw new Exception('El carrito de compras está vacío. Por favor, añada productos.');
            }

            // --- Base Validation for all payment methods ---
            $rules = [
                'nombre_completo' => 'required|string|max:255',
                'correo' => 'required|email|max:255',
                'telefono' => 'nullable|string|max:20',
                'direccion' => 'required|string|max:255',
                'ciudad' => 'required|string|max:100',
                'codigo_postal' => 'nullable|string|max:10',
                'promoCode' => 'nullable|string|max:50',
                'paymentMethod' => 'required|string|in:credit-debit-card,mobile-payment',
                'monto' => 'required|numeric|min:0', // This is the total amount sent from the frontend
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|integer|exists:products,id',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.price' => 'required|numeric|min:0', // Validate the adjusted price sent from frontend
                'items.*.selected_date' => 'nullable|date', // New: Validate selected date per item
                'items.*.selected_time' => 'nullable|string', // New: Validate selected time per item
                'items.*.product_name' => 'required|string|max:255', // New: Validate product name per item
                'items.*.product_description' => 'nullable|string', // New: Validate product description per item
                'items.*.client_type' => 'nullable|string|in:adultOrOver6,under6', // New: Validate client type per item
                'items.*.uniqueId' => 'required|string', // New: Validate uniqueId per item
            ];

            // --- Conditional Validation for Mobile Payment ---
            if ($request->input('paymentMethod') === 'mobile-payment') {
                $rules = array_merge($rules, [
                    'banco_remitente' => 'required|string|max:255',
                    'numero_telefono_remitente' => 'required|string|max:20',
                    'cedula_remitente' => 'required|string|max:20',
                    'numero_referencia_pago' => 'required|string|max:50|unique:tickets,numero_referencia_pago',
                ]);
            }

            // --- Conditional Validation for Credit/Debit Card Payment ---
            if ($request->input('paymentMethod') === 'credit-debit-card') {
                // Get current year for expiry date validation
                $currentYearFull = (int) date('Y');
                $currentMonth = (int) date('m');

                $rules = array_merge($rules, [
                    'card_number' => 'required|string|regex:/^\d{13,19}$/', // Only digits, 13-19 length
                    'card_holder_name' => 'required|string|max:255',
                    'card_expiry_month' => 'required|integer|min:1|max:12',
                    'card_expiry_year' => [
                        'required',
                        'integer',
                        'min:' . $currentYearFull, // Minimum current full year
                        'max:' . ($currentYearFull + 10), // Maximum 10 years in the future
                        // Custom rule to validate that the card has not expired
                        function ($attribute, $value, $fail) use ($request, $currentYearFull, $currentMonth) {
                            $expiryMonth = (int) $request->input('card_expiry_month');
                            $expiryYear = (int) $value; // value is the full year

                            // If expiry year is current year, month must be equal or greater than current month
                            if ($expiryYear === $currentYearFull && $expiryMonth < $currentMonth) {
                                $fail('La fecha de vencimiento de la tarjeta no es válida o ya ha expirado.');
                            }
                        },
                    ],
                    'card_cvv' => 'required|string|digits_between:3,4', // 3 or 4 digits for CVV
                    'numero_referencia_pago' => 'nullable|string|max:50', // Optional for credit/debit card
                ]);
            }

            $validatedData = $request->validate($rules);

            // Aseguramos que $finalAmount siempre se defina
            $totalCalculatedBackend = 0;
            $itemsForTicket = [];
            $itemsForVenta = [];

            // --- Revalidar y Calcular Total en Backend ---
            foreach ($validatedData['items'] as $itemData) {
                $product = Product::find($itemData['product_id']);
                if (!$product) {
                    throw new Exception("Producto con ID {$itemData['product_id']} no encontrado.");
                }
                $quantity = $itemData['quantity'];

                // MODIFICATION: Adjust bracelet price based on the day of the week on the backend
                $itemPrice = $product->price; // Base price from the product model

                // Only apply dynamic pricing for 'Brazalete' and 'Pass Baby Park' categories
                if ($product->category === "Brazalete" || $product->category === "Pass Baby Park") {
                    $selectedDate = $itemData['selected_date']; // Use the selected_date from the item data
                    $dayOfWeek = (new \DateTime($selectedDate))->format('w'); // 0 (Sun) to 6 (Sat)

                    if ($product->category === "Pass Baby Park") {
                        $itemPrice = 6.0; // Fixed price for Baby Park
                    } elseif ($dayOfWeek >= 1 && $dayOfWeek <= 5) { // Monday (1) to Friday (5)
                        $itemPrice = 5.0;
                    } elseif ($dayOfWeek == 0 || $dayOfWeek == 6) { // Sunday (0) or Saturday (6)
                        $itemPrice = 6.0;
                    }
                }

                $itemSubtotal = $itemPrice * $quantity;
                $totalCalculatedBackend += $itemSubtotal;

                $itemsForTicket[] = [
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $itemPrice, // Use the price adjusted by the backend
                    'subtotal' => $itemSubtotal,
                    // New fields from frontend (including the dynamically generated product_name)
                    'selected_date' => $itemData['selected_date'],
                    'selected_time' => $itemData['selected_time'] ?? null,
                    'product_name' => $itemData['product_name'], // This is the key part: it uses the name from frontend
                    'product_description' => $itemData['product_description'] ?? null,
                    'client_type' => $itemData['client_type'] ?? null,
                    'uniqueId' => $itemData['uniqueId'],
                ];

                $itemsForVenta[] = [
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $itemPrice, // Use the price adjusted by the backend
                    'subtotal' => $itemSubtotal,
                    'created_at' => now(),
                    'updated_at' => now(),
                    // New fields from frontend (including the dynamically generated product_name)
                    'selected_date' => $itemData['selected_date'],
                    'selected_time' => $itemData['selected_time'] ?? null,
                    'product_name' => $itemData['product_name'], // This is the key part: it uses the name from frontend
                    'product_description' => $itemData['product_description'] ?? null,
                    'client_type' => $itemData['client_type'] ?? null,
                    'uniqueId' => $itemData['uniqueId'],
                ];
            }

            // Apply promotion if exists
            $descuento = $this->applyPromotion($validatedData['promoCode'] ?? null, $totalCalculatedBackend);
            $finalAmount = $totalCalculatedBackend - $descuento;

            // Security validation for the final amount
            // Allow a small tolerance for floating point errors
            if (abs($validatedData['monto'] - $finalAmount) > 0.02) {
                Log::error('Error de cálculo del monto final.', [
                    'frontend_monto' => $validatedData['monto'],
                    'backend_monto' => $finalAmount,
                    'diff' => abs($validatedData['monto'] - $finalAmount)
                ]);
                throw new Exception("Error de cálculo del monto final. El monto enviado no coincide con el calculado en el servidor.");
            }

            DB::beginTransaction(); // Start a database transaction

            // --- Create the Ticket (Order) ---
            $ticket = Ticket::create([
                'user_id' => $request->user() ? $request->user()->id : null,
                'order_number' => 'ORD-' . Str::upper(Str::random(10)),
                'nombre_completo' => $validatedData['nombre_completo'],
                'correo' => $validatedData['correo'],
                'telefono' => $validatedData['telefono'] ?? null,
                'direccion' => $validatedData['direccion'],
                'ciudad' => $validatedData['ciudad'],
                'codigo_postal' => $validatedData['codigo_postal'] ?? null,
                'promo_code' => $validatedData['promoCode'] ?? null,
                'monto_total' => $finalAmount,
                'payment_method' => $validatedData['paymentMethod'],
                // Initial status based on payment method
                'status' => ($validatedData['paymentMethod'] === 'mobile-payment') ? 'pending_payment_mobile' : 'pending_payment_card', // Assuming 'credit-debit-card'
                // Mobile Payment Fields (saved if they exist in validatedData, otherwise null)
                'banco_remitente' => $validatedData['banco_remitente'] ?? null,
                'numero_telefono_remitente' => $validatedData['numero_telefono_remitente'] ?? null,
                'cedula_remitente' => $validatedData['cedula_remitente'] ?? null,
                // Credit/Debit Card Fields (saved if they exist in validatedData, otherwise null)
                'card_number' => $validatedData['card_number'] ?? null,
                'card_holder_name' => $validatedData['card_holder_name'] ?? null,
                'card_expiry_month' => $validatedData['card_expiry_month'] ?? null,
                'card_expiry_year' => $validatedData['card_expiry_year'] ?? null,
                'card_cvv' => $validatedData['card_cvv'] ?? null,
                // numero_referencia_pago (saved if it exists in validatedData, otherwise null)
                'numero_referencia_pago' => $validatedData['numero_referencia_pago'] ?? null,
            ]);

            // --- Create Ticket Items ---
            foreach ($itemsForTicket as $item) {
                TicketItem::create([
                    'ticket_id' => $ticket->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['subtotal'],
                    // New fields to save in TicketItem (including the dynamically generated product_name)
                    'selected_date' => $item['selected_date'],
                    'selected_time' => $item['selected_time'],
                    'product_name' => $item['product_name'], // This is where the correct name is stored
                    'product_description' => $item['product_description'],
                    'client_type' => $item['client_type'],
                    'uniqueId' => $item['uniqueId'],
                ]);
            }

            // Obtener la fecha de uso del ticket de la sesión
            $fechaUsoTicket = Session::get('purchaseDate');

            //dd($fechaUsoTicket, '2. Valor de purchaseDate recuperado de la sesión (store)');

            // --- CREATE THE INVOICE DIRECTLY HERE ---
            $numeroFactura = 'FAC-' . Str::upper(Str::random(8)) . '-' . $ticket->id;

            $factura = Factura::create([
                'ticket_id' => $ticket->id,
                'user_id' => $request->user() ? $request->user()->id : null,
                'numero_factura' => $numeroFactura,
                'monto_total' => $finalAmount,
                'fecha_emision' => now(),
                'fecha_uso_ticket' => $fechaUsoTicket, // AÑADIDO: Guardar la fecha de uso del ticket
                // Initial invoice status based on payment method
                'status' => ($validatedData['paymentMethod'] === 'mobile-payment') ? 'pending_payment_mobile' : 'pending_payment_card', // Assuming 'credit-debit-card'
                // Defensive change: Use ?? null here
                'nombre_completo' => $validatedData['nombre_completo'] ?? null,
                'correo' => $validatedData['correo'] ?? null,
                'telefono' => $validatedData['telefono'] ?? null,
                'direccion' => $validatedData['direccion'] ?? null,
                'ciudad' => $validatedData['ciudad'] ?? null,
                'codigo_postal' => $validatedData['codigo_postal'] ?? null,
                // Mobile Payment Fields (saved if they exist in validatedData, otherwise null)
                'banco_remitente' => $validatedData['banco_remitente'] ?? null,
                'numero_telefono_remitente' => $validatedData['numero_telefono_remitente'] ?? null,
                'cedula_remitente' => $validatedData['cedula_remitente'] ?? null,
                // Credit/Debit Card Fields (saved if they exist in validatedData, otherwise null)
                'card_number' => $validatedData['card_number'] ?? null,
                'card_holder_name' => $validatedData['card_holder_name'] ?? null,
                'card_expiry_month' => $validatedData['card_expiry_month'] ?? null,
                'card_expiry_year' => $validatedData['card_expiry_year'] ?? null,
                'card_cvv' => $validatedData['card_cvv'] ?? null,
                // numero_referencia_pago (saved if it exists in validatedData, otherwise null)
                'numero_referencia_pago' => $validatedData['numero_referencia_pago'] ?? null,
            ]);

            // If your `tickets` table has `factura_id`, make sure to update it:
            $ticket->factura_id = $factura->id;
            $ticket->save();

            // --- Create Sales Records ---
            foreach ($itemsForVenta as $item) {
                $ventaData = [
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['subtotal'],
                    'user_id' => $request->user() ? $request->user()->id : null,
                    'ticket_id' => $ticket->id,
                    'order_number' => $ticket->order_number,
                    'factura_id' => $factura->id,
                    'fecha' => now()->toDateString(),
                    // New fields to save in Venta (including the dynamically generated product_name)
                    'selected_date' => $item['selected_date'],
                    'selected_time' => $item['selected_time'],
                    'product_name' => $item['product_name'], // This is where the correct name is stored
                    'product_description' => $item['product_description'],
                    'client_type' => $item['client_type'],
                    'uniqueId' => $item['uniqueId'],
                ];
                Venta::create($ventaData);
            }

            DB::commit(); // Commit the transaction

            // Clear the cart from the session
            try {
                session()->forget('cartItems');
                session()->forget('totalAmount'); // También limpiar el totalAmount
                session()->forget('purchaseDate'); // AÑADIDO: Limpiar la fecha de compra de la sesión
            } catch (Exception $e) {
                Log::error('Error al limpiar el carrito de la sesión: ' . $e->getMessage());
            }

            // --- Redirect to the success view with flashed data ---
            return redirect()->route('success')->with([
                'order_number' => $ticket->order_number,
                'payment_method' => $validatedData['paymentMethod'],
                'factura_id' => $factura->id,
                'total_amount' => $finalAmount,
                'numero_factura' => $factura->numero_factura,
            ]);
        } catch (ValidationException $e) {
            // Rollback only if a transaction has been started.
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (Exception $e) {
            // Rollback only if a transaction has been started.
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            Log::error('Error en el proceso de checkout: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()->withErrors(['checkout' => 'Hubo un problema inesperado al procesar su pedido. Por favor, inténtelo de nuevo más tarde.'])->withInput();
        }
    }

    private function applyPromotion(?string $promoCode, float $amount): float
    {
        if (empty($promoCode)) {
            return 0;
        }

        $promotion = Promotion::where('code', $promoCode)
            ->where('is_active', true)
            ->first();

        if (!$promotion) {
            return 0;
        }

        $now = now();
        if ($promotion->starts_at && $now->lt($promotion->starts_at)) {
            return 0;
        }
        if ($promotion->expires_at && $now->gt($promotion->expires_at)) {
            return 0;
        }

        if ($promotion->usage_limit !== null && $promotion->used_count >= $promotion->usage_limit) {
            return 0;
        }

        $discount = 0;
        if ($promotion->type === 'percentage') {
            $discount = $amount * ($promotion->value / 100);
        } elseif ($promotion->type === 'fixed') {
            $discount = $promotion->value;
        }

        return min($discount, $amount);
    }
}
