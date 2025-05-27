<?php

// app/Http/Controllers/ShoppingCartController.php
namespace App\Http\Controllers;

use Inertia\Inertia;

class ShoppingCartController extends Controller
{
    public function index()
    {
        return Inertia::render('ShoppingCart/ShoppingCart');
    }
}
