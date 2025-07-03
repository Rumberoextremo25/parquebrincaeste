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
            // Validate the incoming cart data, including the additional fields sent by Tienda.jsx
            $validatedData = $request->validate([
                'fecha' => 'required|date',
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|integer|exists:products,id',
                'items.*.quantity' => 'required|integer|min:1',
                // IMPORTANT: Validate the 'price' coming from the frontend, as it's the adjusted price
                'items.*.price' => 'required|numeric|min:0',
                'items.*.selected_date' => 'nullable|date',
                'items.*.selected_time' => 'nullable|string',
                'items.*.product_name' => 'required|string|max:255',
                'items.*.product_description' => 'nullable|string',
                'items.*.client_type' => 'nullable|string|in:adultOrOver6,under6',
                'items.*.uniqueId' => 'required|string', // Validate the uniqueId
            ]);

            $cartItemsDetails = [];
            $totalCompra = 0;

            foreach ($validatedData['items'] as $item) {
                // Optional: You can still fetch the product from the database to verify its existence
                // or to retrieve other properties not sent from the frontend (e.g., actual product category).
                // However, for the price, we will use the adjusted price sent from the frontend.
                $productFromDb = Product::findOrFail($item['product_id']);

                // KEY MODIFICATION: Use the price coming from the frontend,
                // as it is the price adjusted by the weekday/weekend logic in Tienda.jsx.
                $itemPrice = $item['price'];
                $itemSubtotal = $item['quantity'] * $itemPrice;
                $totalCompra += $itemSubtotal;

                $cartItemsDetails[] = [
                    // These fields match the "flattened" structure that Checkout.jsx expects
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $itemPrice, // Use the adjusted price from the frontend
                    'selectedDate' => $item['selected_date'],
                    'selectedTime' => $item['selected_time'],
                    'product_name' => $item['product_name'], // Use the name from the frontend
                    'product_description' => $item['product_description'], // Use the description from the frontend
                    'clientType' => $item['client_type'], // Use the client type from the frontend
                    'subtotal' => $itemSubtotal,
                    'category' => $productFromDb->category, // You can get the category from the DB if not sent from frontend
                    'uniqueId' => $item['uniqueId'], // Use the uniqueId sent from the frontend
                ];
            }

            // Save the cart details to the session
            Session::put('cartItems', $cartItemsDetails);
            Session::put('totalAmount', $totalCompra);
            Session::put('purchaseDate', $validatedData['fecha']);

            //dd(Session::get('purchaseDate'), '1. Valor de purchaseDate después de guardarlo en la sesión (comprar)');

            // Redirect to the checkout route
            return redirect()->route('checkout.show');
        } catch (Exception $e) {
            // Error handling
            return redirect()->back()->withErrors(['checkout' => 'Hubo un problema al procesar su pedido. Por favor, inténtelo de nuevo: ' . $e->getMessage()]);
        }
    }
}
