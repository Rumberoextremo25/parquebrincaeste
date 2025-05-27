<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;

class ProductDetailsController extends Controller
{
    public function product(Request $request)
    {
        // Inicializar el precio por defecto  
        $precio = 0;  // Precio por defecto para Lunes a Jueves  

        // Verificar si se ha enviado la fecha  
        if ($request->has('fecha')) {
            $fechaSeleccionada = $request->input('fecha');
            $day = date('N', strtotime($fechaSeleccionada)); // 1 (Lunes) a 7 (Domingo)  

            // Calcular el precio basado en el día de la semana  
            if ($day >= 0) { // 5 (Jueves) a 7 (Domingo)  
                $precio = 5;
            } else {
                $precio = 6;
            }
        }

        return Inertia::render('Product/ProductDetails', [
            'precio' => $precio,
        ]);
    }
}
