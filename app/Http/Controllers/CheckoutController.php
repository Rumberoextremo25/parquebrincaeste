<?php

namespace App\Http\Controllers;

use App\Models\Factura;
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
                'paymentMethod' => 'required|string|in:in-store,mobile-payment',
                'monto' => 'required|numeric|min:0', // Este es el monto que viene del frontend
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
                    'numero_referencia_pago' => 'required|string|max:50|unique:tickets,numero_referencia_pago',
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
                $itemPrice = $product->price;
                $itemSubtotal = $itemPrice * $quantity;
                $totalCalculatedBackend += $itemSubtotal;

                $itemsForTicket[] = [
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $itemPrice,
                    'subtotal' => $itemSubtotal,
                ];

                $itemsForVenta[] = [
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $itemPrice,
                    'subtotal' => $itemSubtotal,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Aplicar promoción si existe
            $descuento = $this->applyPromotion($validatedData['promoCode'] ?? null, $totalCalculatedBackend);
            $finalAmount = $totalCalculatedBackend - $descuento;

            // Validación de seguridad para el monto final
            if (abs($validatedData['monto'] - $finalAmount) > 0.02) {
                throw new Exception("Error de cálculo del monto final. El monto enviado no coincide con el calculado en el servidor.");
            }

            DB::beginTransaction(); // Inicia una transacción de base de datos

            // --- Crear el Ticket (Orden) ---
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
                'status' => ($validatedData['paymentMethod'] === 'mobile-payment') ? 'pending_payment_mobile' : 'pending_payment_cash',
                'banco_remitente' => $validatedData['banco_remitente'] ?? null,
                'numero_telefono_remitente' => $validatedData['numero_telefono_remitente'] ?? null,
                'cedula_remitente' => $validatedData['cedula_remitente'] ?? null,
                'numero_referencia_pago' => $validatedData['numero_referencia_pago'] ?? null,
            ]);

            // --- Crear Ticket Items ---
            foreach ($itemsForTicket as $item) {
                TicketItem::create([
                    'ticket_id' => $ticket->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['subtotal'],
                ]);
            }

            // --- CREAR LA FACTURA DIRECTAMENTE AQUÍ ---
            $numeroFactura = 'FAC-' . Str::upper(Str::random(8)) . '-' . $ticket->id;

            $factura = Factura::create([
                'ticket_id' => $ticket->id,
                'user_id' => $request->user() ? $request->user()->id : null,
                'numero_factura' => $numeroFactura,
                'monto_total' => $finalAmount,
                'fecha_emision' => now(),
                'status' => ($validatedData['paymentMethod'] === 'mobile-payment') ? 'pending_payment_mobile' : 'pending_payment_cash',
                // --- CAMBIO DEFENSIVO: Usar ?? null aquí ---
                'nombre_completo' => $validatedData['nombre_completo'] ?? null,
                'correo' => $validatedData['correo'] ?? null,
                'telefono' => $validatedData['telefono'] ?? null,
                'direccion' => $validatedData['direccion'] ?? null,
                'ciudad' => $validatedData['ciudad'] ?? null,
                'codigo_postal' => $validatedData['codigo_postal'] ?? null,
                'banco_remitente' => $validatedData['paymentMethod'] === 'mobile-payment' ? ($validatedData['banco_remitente'] ?? null) : null,
                'numero_telefono_remitente' => $validatedData['paymentMethod'] === 'mobile-payment' ? ($validatedData['numero_telefono_remitente'] ?? null) : null,
                'cedula_remitente' => $validatedData['paymentMethod'] === 'mobile-payment' ? ($validatedData['cedula_remitente'] ?? null) : null,
                'numero_referencia_pago' => $validatedData['paymentMethod'] === 'mobile-payment' ? ($validatedData['numero_referencia_pago'] ?? null) : null,
            ]);

            // Si tu tabla `tickets` tiene `factura_id`, asegúrate de actualizarlo:
            $ticket->factura_id = $factura->id;
            $ticket->save();

            // --- Crear Registros de Venta ---
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
                ];
                Venta::create($ventaData);
            }

            DB::commit(); // Confirma la transacción

            // Limpia el carrito de la sesión
            try {
                session()->forget('cartItems');
            } catch (Exception $e) {
                Log::error('Error al limpiar el carrito de la sesión: ' . $e->getMessage());
            }

            // --- Redireccionar a la vista de éxito con datos flasheados ---
            return redirect()->route('success')->with([
                'order_number' => $ticket->order_number,
                'payment_method' => $validatedData['paymentMethod'],
                'factura_id' => $factura->id,
                'total_amount' => $finalAmount,
                'numero_factura' => $factura->numero_factura,
            ]);

        } catch (ValidationException $e) {
            // El rollback solo se intenta si la transacción ya había comenzado.
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (Exception $e) {
            // El rollback solo se intenta si la transacción ya había comenzado.
            //dd($e);
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            Log::error('Error en el proceso de checkout: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            // dd($e->getMessage()); // Descomentar para ver el error exacto si persiste
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
