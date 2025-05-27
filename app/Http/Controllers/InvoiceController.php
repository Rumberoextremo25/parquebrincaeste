<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice; // Asegúrate de tener el modelo Invoice
use Barryvdh\DomPDF\Facade\PDF; // Importar la clase PDF

class InvoiceController extends Controller
{
    public function purchaseSuccess(Request $request)
    {
        // Aquí deberías tener la lógica para obtener la información del pedido
        $invoiceData = [
            'id' => $request->input('invoice_id'),
            'amount' => $request->input('amount'),
            'date' => now(),
            'customer_name' => $request->input('customer_name'),
            // Agrega más campos según sea necesario
        ];

        // Guardar en la base de datos
        $invoice = Invoice::create($invoiceData);

        // Generar PDF
        $pdf = PDF::loadView('invoices.invoice', compact('invoice'));

        return view('success', compact('invoice', 'pdf'));
    }

    public function download($id)
    {
        $invoice = Invoice::findOrFail($id);
        $pdf = PDF::loadView('invoices.invoice', compact('invoice'));

        // Abre el PDF en el navegador
        return $pdf->stream('factura_' . $invoice->id . '.pdf');
    }
}
