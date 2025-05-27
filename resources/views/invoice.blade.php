<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Factura #{{ $invoice->id }}</title>
    <style>
        /* Estilos para el PDF */
        body {
            font-family: Arial, sans-serif;
        }
        .invoice {
            padding: 20px;
            border: 1px solid #000;
        }
    </style>
</head>
<body>
    <div class="invoice">
        <h1>Factura #{{ $invoice->id }}</h1>
        <p>Cliente: {{ $invoice->customer_name }}</p>
        <p>Monto Total: ${{ $invoice->amount }}</p>
        <p>Fecha: {{ $invoice->date }}</p>
    </div>
</body>
</html>