<?php

namespace App\Http\Controllers;

use App\Models\Product; // Asegúrate de que este modelo exista
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        // Obtener todos los productos
        $products = Product::all();

        return response()->json($products); // Retornar los productos en formato JSON
    }
}