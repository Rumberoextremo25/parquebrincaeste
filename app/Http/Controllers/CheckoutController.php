<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Cart;
use App\Models\Factura;

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
        // Validar la entrada
        $request->validate([
            'nombre_completo' => 'required|string|max:255',
            'correo' => 'required|email|max:255',
            'telefono' => 'required|string|max:15',
            'direccion' => 'required|string|max:255',
            'ciudad' => 'required|string|max:100',
            'codigo_postal' => 'required|string|max:10',
            'total' => 'required|numeric',
            'paymentMethod' => 'required|string|in:mobile-payment,zelle,in-store', // Validar método de pago
        ]);

        // Crear la factura
        $factura = Factura::create([
            'user_id' => $request->user()->id,
            'nombre_completo' => $request->nombre_completo,
            'correo' => $request->correo,
            'telefono' => $request->telefono,
            'direccion' => $request->direccion,
            'ciudad' => $request->ciudad,
            'codigo_postal' => $request->codigo_postal,
            'total' => $request->total,
        ]);

        // Redirigir según el método de pago
        if ($request->paymentMethod === 'in-store') {
            return Inertia::render('Checkout/Success'); // Redirigir a la página de éxito
        } else {
            // Aquí puedes agregar la lógica para manejar el pago (por ejemplo, redirigir a una pasarela de pago)
            return Inertia::render('Payment/Payment'); // Redirigir a la vista de pago
        }
    }

    public function success()
    {
        return Inertia::render('Checkout/Success');
    }
}
