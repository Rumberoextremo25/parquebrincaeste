<?php

namespace App\Http\Controllers;

use App\Models\Factura; // Asegúrate de importar tus modelos
use App\Models\Venta;
use App\Models\Product; // ¡Importante para revalidar los precios!
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Promotion;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; // ¡Asegúrate de que esta línea esté presente!
use Exception; // Para manejar excepciones de forma más clara
use Illuminate\Validation\ValidationException; // Importar la clase de excepción de validación

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Obtener los items del carrito desde la sesión
        // En tu método 'comprar' del TiendaController, estás enviando cartItemsDetails
        // que ya tiene los detalles completos del producto (id, name, price, quantity, subtotal).
        // Si vienes de esa ruta, esta variable 'cartItems' ya estará llena.
        $cartItems = session()->get('cartItems', []);

        // *Mejora Opcional:* Si el carrito está vacío, podrías redirigir al usuario
        // de vuelta a la tienda o mostrar un mensaje.
        if (empty($cartItems)) {
            // Option 1: Redirect with Inertia (preferred for SPA flow)
            //return redirect()->route('tienda.index')->with('error', 'Tu carrito está vacío. Añade productos para proceder al pago.');
            // Option 2: Render a specific empty cart component (if you have one)
            // return Inertia::render('Checkout/EmptyCart', ['message' => 'Tu carrito está vacío.']);
        }

        return Inertia::render('Checkout/Checkout', [
            'cartItems' => $cartItems, // Se pasan los items para que el frontend los use como initialCartItems
            'user' => $user, // Datos del usuario autenticado
        ]);
    }

    public function store(Request $request)
    {
        try {
            // --- VALIDACIÓN DE DATOS DEL FORMULARIO DE CHECKOUT ---
            $validatedData = $request->validate([
                'nombre_completo' => 'required|string',
                'correo' => 'required|email',
                'telefono' => 'required|string',
                'direccion' => 'required|string',
                'ciudad' => 'required|string',
                'codigo_postal' => 'required|string',
                'promoCode' => 'nullable|string',
                'paymentMethod' => 'required|string|in:mobile-payment,in-store',
                'nombre_banco' => 'nullable|string|required_if:paymentMethod,mobile-payment',
                'numero_telefono' => 'nullable|string|required_if:paymentMethod,mobile-payment',
                'cedula' => 'nullable|string|required_if:paymentMethod,mobile-payment',
                'clave_dinamica' => 'nullable|string|required_if:paymentMethod,mobile-payment',
                'monto' => 'required|numeric|min:0', // El monto enviado desde el frontend (se revalidará)

                // --- VALIDACIÓN DE LOS ITEMS DEL CARRITO ---
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|integer|exists:products,id',
                'items.*.quantity' => 'required|integer|min:1',
            ]);

            DB::beginTransaction(); // Iniciar una transacción

            $totalCalculatedBackend = 0;
            $itemsForFactura = [];

            // --- REVALIDAR Y CALCULAR EL TOTAL EN EL BACKEND (CRUCIAL POR SEGURIDAD) ---
            foreach ($validatedData['items'] as $itemData) {
                $product = Product::findOrFail($itemData['product_id']); // Obtener el producto de la DB
                $quantity = $itemData['quantity'];

                $itemSubtotal = $product->price * $quantity;
                $totalCalculatedBackend += $itemSubtotal;

                $itemsForFactura[] = [
                    'product_id' => $product->id,
                    'cantidad' => $quantity,
                    'precio' => $product->price,
                    'subtotal' => $itemSubtotal,
                ];
            }

            // Aplicar promoción si existe (al total calculado en el backend)
            $descuento = $this->applyPromotion($validatedData['promoCode'] ?? null, $totalCalculatedBackend);
            $montoFinal = $totalCalculatedBackend - $descuento;

            // *Consideración:* Aquí podrías añadir una validación extra
            // para asegurarte de que $validatedData['monto'] (enviado desde el frontend)
            // es igual o muy cercano a $montoFinal. Si hay una discrepancia grande,
            // podría ser un intento de manipulación o un error de cálculo en el frontend.
            // if (abs($validatedData['monto'] - $montoFinal) > 0.01) {
            //     throw new Exception("Error de cálculo del monto. Por favor, intente de nuevo.");
            // }

            // Crear la factura
            $factura = Factura::create([
                'nombre_completo' => $validatedData['nombre_completo'],
                'correo' => $validatedData['correo'],
                'telefono' => $validatedData['telefono'],
                'direccion' => $validatedData['direccion'],
                'ciudad' => $validatedData['ciudad'],
                'codigo_postal' => $validatedData['codigo_postal'],
                'promo_code' => $validatedData['promoCode'] ?? null,
                'metodo_pago' => $validatedData['paymentMethod'],
                'total' => $montoFinal,
                'estatus' => $validatedData['paymentMethod'] === 'mobile-payment' ? 'pendiente_pago_movil' : 'pendiente_caja',
            ]);

            // Crear las ventas asociadas a la factura
            foreach ($itemsForFactura as $item) {
                Venta::create([
                    'factura_id' => $factura->id,
                    'producto_id' => $item['product_id'],
                    'cantidad' => $item['cantidad'],
                    'precio' => $item['precio'],
                    'subtotal' => $item['subtotal'],
                ]);
            }

            // Procesar el pago si es mediante pago móvil
            if ($validatedData['paymentMethod'] === 'mobile-payment') {
                $this->processMobilePayment($factura, [
                    'nombre_banco' => $validatedData['nombre_banco'],
                    'numero_telefono' => $validatedData['numero_telefono'],
                    'cedula' => $validatedData['cedula'],
                    'clave_dinamica' => $validatedData['clave_dinamica'],
                    'monto_pagado' => $validatedData['monto'],
                ]);
            }

            DB::commit(); // Confirmar la transacción

            // Limpiar el carrito de la sesión después de una compra exitosa
            session()->forget('cartItems');

            // Retornar la vista de éxito usando Inertia
            return Inertia::render('Checkout/Success', [
                'message' => '¡Tu compra ha sido procesada con éxito!',
                'facturaId' => $factura->id,
                'totalFinal' => $montoFinal,
            ]);

        } catch (ValidationException $e) {
            DB::rollBack();
            // Esto asegura que Inertia reciba los errores de validación correctamente
            // y tu componente de React pueda mostrarlos (usando Inertia's `props.errors`).
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (Exception $e) {
            DB::rollBack(); // Revertir la transacción en caso de cualquier otro error
            Log::error('Error en el proceso de checkout: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            // Redirigir a la página anterior con un error genérico.
            // Inertia lo capturará y el `props.errors.checkout` estará disponible.
            return redirect()->back()->withErrors(['checkout' => 'Hubo un problema inesperado al procesar su pedido. Por favor, inténtelo de nuevo más tarde.'])->withInput();
        }
    }

    private function applyPromotion(?string $promoCode, float $monto): float
    {
        if (empty($promoCode)) {
            return 0;
        }

        // 1. Buscar el código de promoción en la base de datos
        $promotion = Promotion::where('code', $promoCode)
                              ->where('is_active', true) // Solo promociones activas
                              ->first();

        // Si no se encuentra la promoción o no está activa
        if (!$promotion) {
            return 0;
        }

        // 2. Validar fechas de inicio y expiración
        $now = now(); // Carbon instance of current time
        if ($promotion->starts_at && $now->lt($promotion->starts_at)) {
            return 0; // La promoción aún no ha comenzado
        }
        if ($promotion->expires_at && $now->gt($promotion->expires_at)) {
            return 0; // La promoción ha expirado
        }

        // 3. Validar límite de uso
        if ($promotion->usage_limit !== null && $promotion->used_count >= $promotion->usage_limit) {
            return 0; // El código de promoción ha alcanzado su límite de uso
        }

        // 4. Calcular el descuento según el tipo
        $discount = 0;
        if ($promotion->type === 'percentage') {
            $discount = $monto * $promotion->value;
        } elseif ($promotion->type === 'fixed') {
            $discount = $promotion->value;
        }

        // Asegurarse de que el descuento no sea mayor que el monto total
        return min($discount, $monto);
    }

    private function processMobilePayment($factura, $paymentDetails = [])
    {
        $response = $this->callBankApi($factura, $paymentDetails);

        if ($response['status'] !== 'success') {
            $factura->update(['estatus' => 'pago_movil_fallido']);
            // Es crucial lanzar una excepción aquí para que la transacción se revierta
            throw new Exception('Error en el procesamiento del pago móvil: ' . $response['message']);
        }

        $factura->update(['estatus' => 'completado']);
    }

    private function callBankApi($factura, $paymentDetails)
    {
        // Simulación de una llamada a la API bancaria
        Log::info('Simulando llamada a API bancaria para factura: ' . $factura->id, $paymentDetails);

        // Ejemplo de simulación de fallo basado en un monto insuficiente
        // Descomenta y ajusta si quieres probar fallos
        // if ($paymentDetails['monto_pagado'] < $factura->total) {
        //     return [
        //         'status' => 'error',
        //         'message' => 'El monto pagado es insuficiente para cubrir el total de la factura.',
        //     ];
        // }

        return [
            'status' => 'success',
            'message' => 'Pago procesado correctamente.',
        ];
    }

    public function success()
    {
        // Esta función es útil si navegas directamente a /checkout/success
        // o si necesitas una página de éxito genérica.
        // Si el método `store` ya redirige directamente a una página de éxito de Inertia,
        // esta ruta podría no ser visitada directamente por Inertia después de una compra.
        return Inertia::render('Checkout/Success', [
            'message' => session('message') ?? 'Gracias por tu compra. Tu pedido está en proceso.',
        ]);
    }
}