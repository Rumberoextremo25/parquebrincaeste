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
use Illuminate\Validation\ValidationException; 
use Inertia\Response;// Importar la clase de excepción de validación

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $cartItems = session()->get('cartItems', []);

        // *Mejora Opcional:* Si el carrito está vacío, podrías redirigir al usuario
        // de vuelta a la tienda o mostrar un mensaje.
        if (empty($cartItems)) {

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
                'nombre_completo' => 'required|string|max:255',
                'correo' => 'required|email|max:255',
                'telefono' => 'required|string|max:20',
                'direccion' => 'required|string|max:255',
                'ciudad' => 'required|string|max:100',
                'codigo_postal' => 'required|string|max:10',
                'promoCode' => 'nullable|string|max:50',
                'paymentMethod' => 'required|string|in:mobile-payment,in-store',
                'monto' => 'required|numeric|min:0', // El monto enviado desde el frontend (se revalidará)

                // --- CAMPOS ESPECÍFICOS PARA PAGO MÓVIL (REQUIRED_IF) ---
                'banco_remitente' => 'nullable|string|max:100|required_if:paymentMethod,mobile-payment',
                'numero_telefono_remitente' => 'nullable|string|max:20|required_if:paymentMethod,mobile-payment',
                'cedula_remitente' => 'nullable|string|max:20|required_if:paymentMethod,mobile-payment',
                'numero_referencia_pago' => 'nullable|string|max:50|required_if:paymentMethod,mobile-payment',
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

            // Validación de seguridad para el monto
            if (abs($validatedData['monto'] - $montoFinal) > 0.01) {
                throw new Exception("Error de cálculo del monto final. Posible manipulación o desincronización.");
            }

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
                    'banco_remitente' => $validatedData['banco_remitente'],
                    'numero_telefono_remitente' => $validatedData['numero_telefono_remitente'],
                    'cedula_remitente' => $validatedData['cedula_remitente'],
                    'numero_referencia_pago' => $validatedData['numero_referencia_pago'],
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
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error en el proceso de checkout: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()->withErrors(['checkout' => 'Hubo un problema inesperado al procesar su pedido. Por favor, inténtelo de nuevo más tarde.'])->withInput();
        }
    }

    /**
     * Retorna una lista de bancos disponibles.
     * Este método será llamado por el frontend para poblar el selector de bancos.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function listarBancos()
    {
        // Aquí puedes obtener los bancos de tu base de datos si tienes una tabla 'bancos'.
        // Por ejemplo:
        // $bancos = \App\Models\Banco::select('id', 'name')->orderBy('name')->get();
        // return Response::json($bancos);

        // Ejemplo de una lista estática de bancos (idealmente, esto vendría de una DB o servicio)
        $bancos = [
            ['id' => '1', 'name' => 'Banco de Venezuela'],
            ['id' => '2', 'name' => 'Banesco'],
            ['id' => '3', 'name' => 'Mercantil'],
            ['id' => '4', 'name' => 'Provincial'],
            ['id' => '5', 'name' => 'BDV'],
            ['id' => '6', 'name' => 'Banplus'],
            ['id' => '7', 'name' => 'BNC'],
            ['id' => '8', 'name' => 'Banco del Tesoro'],
            // Añade más bancos reales aquí
        ];

        return Response::json($bancos);
    }

    /**
     * Aplica una promoción al monto dado si el código es válido.
     *
     * @param  string|null  $promoCode
     * @param  float  $monto
     * @return float
     */
    private function applyPromotion(?string $promoCode, float $monto): float
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
            $discount = $monto * $promotion->value;
        } elseif ($promotion->type === 'fixed') {
            $discount = $promotion->value;
        }

        return min($discount, $monto);
    }

    /**
     * Procesa el pago móvil.
     *
     * @param  \App\Models\Factura  $factura
     * @param  array  $paymentDetails
     * @return void
     * @throws \Exception
     */
    private function processMobilePayment($factura, $paymentDetails = [])
    {
        $response = $this->callBankApi($factura, $paymentDetails);

        if ($response['status'] !== 'success') {
            $factura->update(['estatus' => 'pago_movil_fallido']);
            throw new Exception('Error en el procesamiento del pago móvil: ' . $response['message']);
        }

        $factura->update(['estatus' => 'completado']);
    }

    /**
     * Simula una llamada a la API bancaria para procesar el pago.
     *
     * @param  \App\Models\Factura  $factura
     * @param  array  $paymentDetails
     * @return array
     */
    private function callBankApi($factura, $paymentDetails)
    {
        Log::info('Simulando llamada a API bancaria para factura: ' . $factura->id, [
            'factura_total' => $factura->total,
            'monto_pagado' => $paymentDetails['monto_pagado'] ?? 'N/A',
            'banco_remitente' => $paymentDetails['banco_remitente'] ?? 'N/A',
            'numero_telefono_remitente' => $paymentDetails['numero_telefono_remitente'] ?? 'N/A',
            'cedula_remitente' => $paymentDetails['cedula_remitente'] ?? 'N/A',
            'numero_referencia_pago' => $paymentDetails['numero_referencia_pago'] ?? 'N/A',
        ]);

        return [
            'status' => 'success',
            'message' => 'Pago procesado correctamente.',
        ];
    }

    /**
     * Muestra la página de éxito del checkout.
     *
     * @return \Inertia\Response
     */
    public function success()
    {
        return Inertia::render('Checkout/Success', [
            'message' => session('message') ?? 'Gracias por tu compra. Tu pedido está en proceso.',
        ]);
    }
}