<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Factura;
use App\Models\Venta; // Usamos el modelo Venta
use App\Models\Cart;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        // Obtener el usuario autenticado
        $user = $request->user();

        // Obtener items del carrito desde la base de datos
        $cartItems = Cart::where('user_id', $user->id)->with('product')->get();

        return Inertia::render('Checkout/Checkout', [
            'cartItems' => $cartItems,
            'user' => $user,
        ]);
    }

    public function store(Request $request)
    {
        // Validar los datos de entrada
        $validatedData = $request->validate([
            'nombre_completo' => 'required|string|max:255',
            'correo' => 'required|email|max:255',
            'telefono' => 'required|string|max:15',
            'direccion' => 'required|string|max:255',
            'ciudad' => 'required|string|max:100',
            'codigo_postal' => 'required|string|max:10',
            'promoCode' => 'nullable|string|max:50',
            'paymentMethod' => 'required|string',
            'cartItems' => 'required|array', // Asegúrate de validar los artículos del carrito
            'total' => 'required|numeric', // Asegúrate de que el total sea enviado correctamente
        ]);

        DB::beginTransaction(); // Iniciar una transacción

        try {
            // Aplicar promoción si existe
            $descuento = $this->applyPromotion($validatedData['promoCode'], $validatedData['total']);
            $montoFinal = $validatedData['total'] - $descuento;

            // Crear la factura
            $factura = Factura::create([
                'nombre_completo' => $validatedData['nombre_completo'],
                'correo' => $validatedData['correo'],
                'telefono' => $validatedData['telefono'],
                'direccion' => $validatedData['direccion'],
                'ciudad' => $validatedData['ciudad'],
                'codigo_postal' => $validatedData['codigo_postal'],
                'promo_code' => $validatedData['promoCode'],
                'metodo_pago' => $validatedData['paymentMethod'],
                'total' => $montoFinal,
                'estatus' => $validatedData['paymentMethod'] === 'pago_movil' ? 'completado' : 'pendiente', // Establecer el estatus
            ]);

            // Crear las ventas asociadas a la factura
            foreach ($validatedData['cartItems'] as $item) {
                Venta::create([ // Usamos el modelo Venta
                    'factura_id' => $factura->id,
                    'producto_id' => $item['product']['id'], // Asegúrate de que el índice sea correcto
                    'cantidad' => $item['quantity'],
                    'precio' => $item['product']['price'],
                    'subtotal' => $item['product']['price'] * $item['quantity'], // Agregar subtotal si es necesario
                ]);
            }

            // Procesar el pago si es mediante pago móvil
            if ($validatedData['paymentMethod'] === 'pago_movil') {
                $this->processMobilePayment($factura);
            }

            DB::commit(); // Confirmar la transacción

            // Retornar la vista de éxito usando Inertia
            return Inertia::render('Checkout/Success', [
                'message' => 'Compra realizada con éxito.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack(); // Revertir la transacción en caso de error
            return response()->json(['message' => 'Error al procesar la compra: ' . $e->getMessage()], 500);
        }
    }

    private function applyPromotion($promoCode, $monto)
    {
        // Aquí puedes implementar la lógica para aplicar la promoción
        // Por ejemplo, si el código de promoción es "DESCUENTO10", aplica un 10% de descuento
        if ($promoCode === 'DESCUENTO10') {
            return $monto * 0.10; // 10% de descuento
        }
        return 0; // Sin descuento
    }

    private function processMobilePayment($factura)
    {
        // Aquí implementas la lógica para llamar a la API bancaria
        $response = $this->callBankApi($factura);

        if ($response['status'] !== 'success') {
            // Manejar el error de pago
            throw new \Exception('Error en el procesamiento del pago móvil: ' . $response['message']);
        }

        // Si el pago es exitoso, actualiza el estatus de la factura
        $factura->update(['estatus' => 'completado']);
    }

    private function callBankApi($factura)
    {
        // Simulación de una llamada a la API bancaria
        return [
            'status' => 'success',
            'message' => 'Pago procesado correctamente.',
        ];
    }

    public function success()
    {
        return Inertia::render('Checkout/Success', [
            'message' => session('message'),
        ]); // Asegúrate de que esta vista exista
    }
}
