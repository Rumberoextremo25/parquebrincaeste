<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Exception;
use Inertia\Inertia;
use Illuminate\Http\Request;

class TiendaController extends Controller
{
    public function tienda()
    {
        $products = Product::all();

        return Inertia::render('Tienda/tienda', [
            'products' => $products,
        ]);
    }

    public function comprar(Request $request)
    {
        // Usamos el middleware 'auth' en la ruta (ver web.php),
        // así que si llegamos aquí, el usuario ya está autenticado.
        // Si aún no lo has hecho, agrega ->middleware('auth') a tu ruta POST '/checkout' en web.php.

        try {
            // // 1. Validar los datos de la solicitud entrante
            // $validatedData = $request->validate([
            //     'fecha' => 'required|date',
            //     'items' => 'required|array|min:1', // Asegúrate de que 'items' sea un array y no esté vacío
            //     'items.*.product_id' => 'required|integer|exists:products,id', // Cada ítem debe tener un product_id válido que exista
            //     'items.*.quantity' => 'required|integer|min:1', // Cada ítem debe tener una cantidad >= 1
            // ]);

            // $cartItemsDetails = [];
            // $totalCompra = 0;

            // // 2. Procesar cada ítem del carrito, obtener los detalles completos del producto y calcular el total
            // foreach ($validatedData['items'] as $item) {
            //     // Obtener el producto de la base de datos para obtener su precio real y detalles
            //     $product = Product::findOrFail($item['product_id']);

            //     // Calcular el subtotal para el ítem actual
            //     $itemSubtotal = $item['quantity'] * $product->price;

            //     // Añadir al total general
            //     $totalCompra += $itemSubtotal;

            //     // Preparar los datos detallados del ítem para enviar al frontend
            //     $cartItemsDetails[] = [
            //         'id' => $product->id,
            //         'name' => $product->name,
            //         'description' => $product->description,
            //         'price' => $product->price, // Precio por unidad
            //         'quantity' => $item['quantity'],
            //         'subtotal' => $itemSubtotal, // Total para este ítem específico (cantidad * precio)
            //         'category' => $product->category,
            //         'applicableTo' => $product->applicableTo ?? null,
            //         // Incluye cualquier otro dato del producto que la vista necesite (ej. imagen, slug, etc.)
            //     ];
            // }

            // dd('llego');

            // 3. Renderizar la vista de Inertia Checkout con todos los datos necesarios
            // Asegúrate de que 'Checkout/Checkout' coincida con la ruta de tu componente (ej. resources/js/Pages/Checkout/Checkout.jsx)
            return Inertia::render('Checkout/Checkout', [
                // 'purchaseDate' => $validatedData['fecha'], // Fecha de la compra
                // 'cartItems' => $cartItemsDetails, // Esto ahora contiene los detalles completos y el subtotal
                // 'totalAmount' => $totalCompra,      // Este es el total general calculado de forma segura en el backend
                // 'user' => $request->user(),         // Pasa los datos del usuario autenticado (si está logueado)
            ]);

        } catch (Exception $e) {
            // Manejar cualquier excepción (ej. producto no encontrado, problemas de validación)
            // Se redirige a la página anterior con un mensaje de error
            return redirect()->back()->withErrors(['checkout' => 'Hubo un problema al procesar su pedido. Por favor, inténtelo de nuevo: ' . $e->getMessage()]);
        }
    }
}
