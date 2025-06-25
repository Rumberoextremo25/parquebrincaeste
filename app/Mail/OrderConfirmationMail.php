<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Factura; // Import the Factura model
use Barryvdh\DomPDF\Facade\Pdf; // Import PDF facade

class OrderConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $factura;
    public $ticket;
    public $ticketItems;
    public $customer;
    public $company;

    /**
     * Create a new message instance.
     *
     * @param \App\Models\Factura $factura
     * @return void
     */
    public function __construct(Factura $factura)
    {
        $this->factura = $factura;
        $this->ticket = $factura->ticket; // Access the related ticket
        $this->ticketItems = $factura->ticket->ticketItems; // Access the related ticket items

        // Prepare customer and company data for the email and PDF
        $this->customer = [
            'name' => $this->factura->customer_name,
            'email' => $this->factura->customer_email,
            'phone' => $this->ticket->customer_phone,
            'address' => $this->ticket->shipping_address,
            'city' => $this->ticket->city,
            'postal_code' => $this->ticket->postal_code,
        ];

        $this->company = [
            'name' => 'Brinca Este 2024 C.A',
            'address' => 'Av. Bolívar, Centro Comercial Miranda, Local #10, Charallave, Miranda, Venezuela',
            'phone' => '(0412) 350 88 26',
            'rif' => 'J-505728440',
        ];

        // Ensure ticket items products are loaded for the email view and PDF attachment
        $this->ticketItems->load('product');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Confirmación de Compra - Orden #' . $this->factura->invoice_number,
            to: $this->factura->customer_email, // Send to the customer's email
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.orders.confirmation', // This will be our email Blade view
            with: [
                'factura' => $this->factura,
                'ticket' => $this->ticket,
                'ticketItems' => $this->ticketItems,
                'customer' => $this->customer,
                'company' => $this->company,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        // Generate PDF on the fly for attachment
        $pdf = Pdf::loadView('pdf.invoice', [
            'factura' => $this->factura,
            'ticket' => $this->ticket,
            'ticketItems' => $this->ticketItems,
            'customer' => $this->customer,
            'company' => $this->company,
        ]);

        return [
            Attachment::fromData(fn () => $pdf->output(), 'factura_' . $this->factura->invoice_number . '.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
