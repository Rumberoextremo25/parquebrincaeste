<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use App\Models\Ticket;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InvoiceController extends Controller
{
    /**
     * Display the purchase success page.
     *
     * @param int $invoiceId The ID of the Factura record.
     * @return \Inertia\Response
     */
    public function purchaseSuccess(int $invoiceId)
    {
        // Fetch the factura with its related ticket and ticket items for display
        $factura = Factura::with('ticket.ticketItems.product')->findOrFail($invoiceId);

        return Inertia::render('Checkout/Success', [
            'order_number' => $factura->ticket->order_number,
            'payment_method' => $factura->ticket->payment_method,
            'invoice_id' => $factura->id,
            'customer_name' => $factura->customer_name,
            'total_amount' => $factura->total_amount, // Ensure total_amount is passed
        ]);
    }

    /**
     * Generates and serves the PDF invoice (typically viewed in browser).
     *
     * @param int $id The ID of the Factura record.
     * @return \Illuminate\Http\Response
     */
    public function download(int $id)
    {
        // Fetch the factura with its related ticket and ticket items
        $factura = Factura::with('ticket.ticketItems.product')->findOrFail($id);

        // Prepare data for the PDF view
        $data = [
            'factura' => $factura,
            'ticket' => $factura->ticket,
            'ticketItems' => $factura->ticket->ticketItems,
            'customer' => [
                'name' => $factura->customer_name,
                'email' => $factura->customer_email,
                'address' => $factura->ticket->shipping_address,
                'city' => $factura->ticket->city,
                'postal_code' => $factura->ticket->postal_code,
                'phone' => $factura->ticket->customer_phone,
            ],
            'company' => [
                'name' => 'Brinca Este 2024 C.A',
                'address' => 'Av. Bolívar, Centro Comercial Miranda, Local #10, Charallave, Miranda, Venezuela',
                'phone' => '(0412) 350 88 26',
                'rif' => 'J-505728440',
            ],
        ];

        // Load the blade view ('pdf.invoice') and generate PDF
        $pdf = Pdf::loadView('pdf.invoice', $data);

        // Instead of ->download(), use ->stream() to try and display in browser
        // Most browsers will interpret 'application/pdf' and try to open it.
        return $pdf->stream('factura_' . $factura->invoice_number . '.pdf');
    }
}
