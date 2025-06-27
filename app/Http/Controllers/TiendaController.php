<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\BcvService;
use Exception;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class TiendaController extends Controller
{
    protected $bcvService;
    public function __construct(BcvService $bcvService)
    {
        $this->bcvService = $bcvService;
    }
    public function tienda()
    {
        $products = Product::all();

        $bcvRate = $this->bcvService->getExchangeRate();

        return Inertia::render('Tienda/tienda', [
            'products' => $products,
            'bcvRate' => $bcvRate,
        ]);
    }

    public function comprar(Request $request)
    {
        try {
            
            $validatedData = $request->validate([
                'fecha' => 'required|date',
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|integer|exists:products,id',
                'items.*.quantity' => 'required|integer|min:1',
            ]);

            $cartItemsDetails = [];
            $totalCompra = 0;

            foreach ($validatedData['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                $itemSubtotal = $item['quantity'] * $product->price;
                $totalCompra += $itemSubtotal;

                $cartItemsDetails[] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'description' => $product->description,
                    'price' => $product->price,
                    'quantity' => $item['quantity'],
                    'subtotal' => $itemSubtotal,
                    'category' => $product->category,
                    'applicableTo' => $product->applicableTo ?? null,
                ];
            }

            // --- INICIO DEL CAMBIO ---

            // Define la URL a la que quieres redirigir.
            // Puede ser una ruta con nombre: route('checkout.page')
            // O una URL directa: '/checkout'
            // $redirectUrl = 'checkout'; // ¡Ajusta esta URL a tu ruta de checkout!

            // Redirige a la URL deseada pasando los datos en la sesión.
            // return Redirect::to($redirectUrl)->with([
            //     'purchaseDate' => $validatedData['fecha'],
            //     'cartItems' => $cartItemsDetails,
            //     'totalAmount' => $totalCompra,
            //     'user' => $request->user(), // El usuario ya suele estar disponible globalmente
            // ]);

            // --- FIN DEL CAMBIO ---

            Session::put('cartItems', $cartItemsDetails);
            Session::put('totalAmount', $totalCompra);
            Session::put('purchaseDate', $validatedData['fecha']);
            return redirect()->route('checkout.show');
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['checkout' => 'Hubo un problema al procesar su pedido. Por favor, inténtelo de nuevo: ' . $e->getMessage()]);
        }
    }
}
