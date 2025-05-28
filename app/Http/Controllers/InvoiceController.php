<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\PDF; // Importar la clase PDF
use Inertia\Inertia;
use App\Models\Factura;

class InvoiceController extends Controller
{
    public function purchaseSuccess($id)
    {
        // Buscar la factura por ID
        $invoice = Factura::findOrFail($id);

        // Generar el PDF usando la vista 'invoices.invoice'
        $pdf = PDF::loadView('invoices.invoice', ['invoice' => $invoice]);

        // Retornar vista de éxito con los datos de la factura y PDF generado
        return inertia('Checkout/Success', [
            'invoice' => $invoice,
            'pdf' => $pdf->output(), // Puedes enviar o usar el PDF como prefieras
        ]);
    }

    public function download($id)
    {
        $invoice = Factura::findOrFail($id);
        $pdf = PDF::loadView('invoices.invoice', ['invoice' => $invoice]);

        // Stream del PDF al navegador
        return $pdf->stream('factura_' . $invoice->id . '.pdf');
    }
}
