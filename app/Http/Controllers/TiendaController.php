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
        // Validar los datos del formulario
        $validatedData = $request->validate([
            'cantidad' => 'required|integer|min:1',
            'fecha' => 'required|date',
            'hora' => 'required|date_format:H:i',
            'tipoTicket' => 'required|string',
            'talla' => 'nullable|string',
            'cantidadMedias' => 'nullable|integer|min:0',
            'tallaMedias' => 'nullable|string',
        ]);

        // Procesar la compra (puedes agregar lógica para guardar en la base de datos, etc.)
        // Aquí puedes calcular el total y cualquier otra lógica necesaria
        $precioMedias = 2;
        $totalMedias = $precioMedias * ($validatedData['cantidadMedias'] ?? 0);
        $total = ($validatedData['cantidad'] * 5) + $totalMedias; // Cambia 5 por tu lógica de precio

        // Redirigir a la vista de Checkout con los datos necesarios
        return Inertia::render('Checkout/Checkout')->with([
            'total' => $total,
            'datos' => $validatedData,
        ]);
    }
}
