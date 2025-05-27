<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class PaymentController extends Controller
{
    public function details(Request $request)
    {
        // Obtener el método de pago
        $paymentMethod = $request->input('payment_method', null);

        // Inicializar un array para los datos de pago
        $paymentData = [];

        // Lógica para cada método de pago
        switch ($paymentMethod) {
            case 'pago_movil':
                $paymentData = $request->validate([
                    'mobile_number' => 'required|string',
                    'bank' => 'required|string',
                    'date' => 'required|date',
                    'amount' => 'required|numeric',
                ]);
                break;

            case 'transferencia':
                $paymentData = $request->validate([
                    'account_number' => 'required|string',
                    'bank' => 'required|string',
                    'account_type' => 'required|string',
                    'date' => 'required|date',
                    'amount' => 'required|numeric',
                ]);
                break;

            case 'tarjeta_credito':
                $paymentData = $request->validate([
                    'card_number' => 'required|string',
                    'expiration_date' => 'required|date_format:m/y',
                    'date' => 'required|date',
                    'amount' => 'required|numeric',
                    'security_code' => 'required|string',
                ]);
                break;

            default:
                return response()->json(['error' => 'Método de pago no válido'], 400);
        }

        // Pasar los datos a la vista
        return Inertia::render('Product/Details', [
            'paymentMethod' => $paymentMethod,
            'paymentData' => $paymentData,
        ]);
    }
}

