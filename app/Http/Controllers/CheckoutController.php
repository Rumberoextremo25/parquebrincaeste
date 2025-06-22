<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Promotion;
use App\Models\Ticket;
use App\Models\TicketItem;
use App\Models\Venta; // Import the Venta model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Exception;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    /**
     * Display the checkout form.
     *
     * @param Request $request
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $cartItems = session()->get('cartItems', []);

        if (empty($cartItems)) {
            // Optional: Handle empty cart, e.g., redirect
        }

        return Inertia::render('Checkout/Checkout', [
            'cartItems' => $cartItems,
            'user' => $user ? $user->toArray() : null,
            'errors' => session('errors') ? session('errors')->getBag('default')->getMessages() : [],
        ]);
    }

    /**
     * Store the order information in the database, including 'tickets', 'ticket_items', and 'ventas'.
     *
     * @param Request $request
     * @return \Inertia\Response|\Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        try {
            if (empty($request->input('items'))) {
                throw new Exception('El carrito de compras está vacío. Por favor, añada productos.');
            }

            // --- Base Validation for all payment methods ---
            $rules = [
                'nombre_completo' => 'required|string|max:255',
                'correo' => 'required|email|max:255',
                'telefono' => 'required|string|max:20',
                'direccion' => 'required|string|max:255',
                'ciudad' => 'required|string|max:100',
                'codigo_postal' => 'required|string|max:10',
                'promoCode' => 'nullable|string|max:50',
                'paymentMethod' => 'required|string|in:in-store,mobile-payment',
                'monto' => 'required|numeric|min:0',
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|integer|exists:products,id',
                'items.*.quantity' => 'required|integer|min:1',
            ];

            // --- Conditional Validation for Mobile Payment ---
            if ($request->input('paymentMethod') === 'mobile-payment') {
                $rules = array_merge($rules, [
                    'banco_remitente' => 'required|string|max:255',
                    'numero_telefono_remitente' => 'required|string|max:20',
                    'cedula_remitente' => 'required|string|max:20',
                    'numero_referencia_pago' => 'required|string|max:50',
                ]);
            }

            $validatedData = $request->validate($rules);

            DB::beginTransaction(); // Start a database transaction

            $totalCalculatedBackend = 0;
            $itemsForTicket = [];
            $itemsForVenta = []; // New array to prepare data for the 'ventas' table

            // --- Revalidate and Calculate Total on Backend ---
            foreach ($validatedData['items'] as $itemData) {
                $product = Product::find($itemData['product_id']);
                if (!$product) {
                    throw new Exception("Producto con ID {$itemData['product_id']} no encontrado.");
                }
                $quantity = $itemData['quantity'];
                $itemPrice = $product->price;
                $itemSubtotal = $itemPrice * $quantity;
                $totalCalculatedBackend += $itemSubtotal;

                $itemsForTicket[] = [
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $itemPrice,
                    'subtotal' => $itemSubtotal,
                ];

                // Prepare data for the 'ventas' table
                $itemsForVenta[] = [
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $itemPrice,
                    'subtotal' => $itemSubtotal, // Assuming 'subtotal' or 'total' per item in 'ventas'
                    // Add any other fields required by your 'ventas' table (e.g., date, user_id, order_id)
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Apply promotion if it exists
            $descuento = $this->applyPromotion($validatedData['promoCode'] ?? null, $totalCalculatedBackend);
            $finalAmount = $totalCalculatedBackend - $descuento;

            // Security validation for the amount
            if (abs($validatedData['monto'] - $finalAmount) > 0.02) {
                throw new Exception("Error de cálculo del monto final. El monto enviado no coincide con el calculado.");
            }

            // --- Create the Ticket (Order) ---
            $ticket = Ticket::create([
                'user_id' => $request->user() ? $request->user()->id : null,
                'order_number' => 'ORD-' . Str::upper(Str::random(10)),
                'customer_name' => $validatedData['nombre_completo'],
                'customer_email' => $validatedData['correo'],
                'customer_phone' => $validatedData['telefono'],
                'shipping_address' => $validatedData['direccion'],
                'city' => $validatedData['ciudad'],
                'postal_code' => $validatedData['codigo_postal'],
                'promo_code' => $validatedData['promoCode'] ?? null,
                'payment_method' => $validatedData['paymentMethod'],
                'total_amount' => $finalAmount,
                'status' => ($validatedData['paymentMethod'] === 'mobile-payment') ? 'pending_payment_mobile' : 'pending_payment_cash',
                'bank_name' => $validatedData['banco_remitente'] ?? null,
                'sender_phone' => $validatedData['numero_telefono_remitente'] ?? null,
                'sender_id_number' => $validatedData['cedula_remitente'] ?? null,
                'reference_number' => $validatedData['numero_referencia_pago'] ?? null,
            ]);

            // --- Create Ticket Items ---
            foreach ($itemsForTicket as $item) {
                TicketItem::create([
                    'ticket_id' => $ticket->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['subtotal'],
                ]);
            }

            // --- Create Venta Records ---
            // If Venta model represents individual line items from a sale,
            // you might need to relate it to the ticket as well, or update its schema.
            // For now, I'll assume Venta can stand alone for simpler recording,
            // but consider adding `ticket_id` to your `ventas` table for better relational integrity.
            foreach ($itemsForVenta as $item) {
                // Assuming 'ventas' table does NOT have a 'ticket_id' directly,
                // and you just want to record the sale.
                // If 'ventas' has a 'factura_id' or 'order_id', you'd set it here.
                // Example if your Venta table also has a user_id:
                $ventaData = [
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['subtotal'],
                    'user_id' => $request->user() ? $request->user()->id : null, // Assuming ventas might also link to user
                    'ticket_id' => $ticket->id, // Consider adding this column to your 'ventas' table
                    'order_number' => $ticket->order_number, // Or this
                ];
                Venta::create($ventaData);
            }

            DB::commit(); // Confirm the transaction

            // Clear the cart from the session
            try {
                session()->forget('cartItems');
            } catch (Exception $e) {
                Log::error('Error al limpiar el carrito de la sesión: ' . $e->getMessage());
            }

            // --- Redirect to Success View ---
            return Inertia::render('Checkout/Success', [
                'order_number' => $ticket->order_number,
                'payment_method' => $validatedData['paymentMethod'],
            ]);

        } catch (ValidationException $e) {
            DB::rollBack();
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error en el proceso de checkout: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()->withErrors(['checkout' => $e->getMessage() ?? 'Hubo un problema inesperado al procesar su pedido. Por favor, inténtelo de nuevo más tarde.'])->withInput();
        }
    }

    /**
     * Applies a promotion to the given amount if the code is valid.
     *
     * @param  string|null  $promoCode
     * @param  float  $amount
     * @return float The discount amount
     */
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

    /**
     * Displays the checkout success page.
     *
     * @param Request $request
     * @return \Inertia\Response
     */
    public function success(Request $request)
    {
        $orderNumber = $request->query('order_number') ?? session('order_number');
        $paymentMethod = $request->query('payment_method') ?? session('payment_method');

        return Inertia::render('Checkout/Success', [
            'order_number' => $orderNumber,
            'payment_method' => $paymentMethod,
        ]);
    }
}