<x-mail::message>
# ¡Gracias por tu compra, {{ $customer['name'] }}!

Tu pedido con el número de orden **#{{ $ticket->order_number }}** ha sido confirmado y procesado exitosamente.

A continuación, un resumen de tu compra:

**Detalles de la Factura:**
* **Número de Factura:** #{{ $factura->invoice_number }}
* **Fecha de Emisión:** {{ $factura->issue_date->format('d/m/Y H:i') }}
* **Monto Total:** ${{ number_format($factura->total_amount, 2) }}
* **Método de Pago:** {{ $ticket->payment_method === 'in-store' ? 'Pago en Caja' : 'Pago Móvil' }}

**Productos Adquiridos:**
<x-mail::table>
| Producto        | Cantidad | Precio Unitario | Subtotal    |
|:----------------|:--------:|:---------------:|:------------|
@foreach($ticketItems as $item)
| {{ $item->product->name }} | {{ $item->quantity }} | ${{ number_format($item->price, 2) }} | ${{ number_format($item->subtotal, 2) }} |
@endforeach
</x-mail::table>

@if($ticket->payment_method === 'in-store')
**Importante:**
Por favor, acude a nuestra tienda con tu **número de orden ({{ $ticket->order_number }})** para cancelar el monto de **${{ number_format($factura->total_amount, 2) }}** y retirar tus productos.
@elseif($ticket->payment_method === 'mobile-payment')
**Estado de tu Pago Móvil:**
Tu pago móvil está siendo procesado. Recibirás una confirmación adicional una vez que se verifique el pago. Por favor, conserva tu número de referencia ({{ $ticket->reference_number ?? 'N/A' }}).
@endif

Hemos adjuntado una copia de tu factura en formato PDF a este correo para tu registro.

Si tienes alguna pregunta, no dudes en contactarnos.

Saludos,
{{ $company['name'] }}

<x-mail::button :url="url('/')">
Visita nuestra Tienda
</x-mail::button>
</x-mail::message>