<?php

namespace App\Http\Controllers;  

use App\Models\Product;
use Inertia\Inertia;
use Illuminate\Http\Request;

class TiendaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only('buy');
    }

    public function tienda()
    {
        $products = Product::all();

        return Inertia::render('Tienda/tienda', [
            'products' => $products,
        ]);
    }

    public function comprar(Request $request)
    {
        // Validar datos del producto y otros datos
        $validatedData = $request->validate([
            'producto_id' => 'required|integer|exists:products,id',
            'nombre' => 'required|string',
            'precio' => 'required|numeric',
            'cantidad' => 'required|integer|min:1',
            'fecha' => 'required|date',
            'hora' => 'required|date_format:H:i',
            'tipoTicket' => 'required|string',
            'talla' => 'nullable|string',
            'cantidadMedias' => 'nullable|integer|min:0',
            'tallaMedias' => 'nullable|string',
        ]);

        // Calcular total basado en cantidad y precio
        $total = $validatedData['cantidad'] * $validatedData['precio'];

        // Crear un array con los datos del item para pasar a la vista
        $cartItem = [
            'producto_id' => $validatedData['producto_id'],
            'nombre' => $validatedData['nombre'],
            'cantidad' => $validatedData['cantidad'],
            'precio' => $validatedData['precio'],
            'total' => $total,
        ];

        // Pasar los datos a la vista de checkout
        return Inertia::render('Checkout/Checkout')->with([
            'cartItems' => [$cartItem], // Puede ser una lista si hay más productos
            'total' => $total,
            'datos' => $validatedData,
        ]);
    }
}
