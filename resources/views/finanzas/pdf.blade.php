<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informe Financiero</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h1 {
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            border: 1px solid #000;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .total {
            font-weight: bold;
            font-size: 1.2em;
        }
    </style>
</head>
<body>
    <h1>Informe Financiero</h1>

    <h2>Resumen</h2>
    <p><strong>Ingresos Totales:</strong> ${{ number_format($ingresosTotales, 2) }}</p>
    <p><strong>Gastos Totales:</strong> ${{ number_format($gastosTotales, 2) }}</p>
    <p class="total"><strong>Beneficio Neto:</strong> ${{ number_format($beneficioNeto, 2) }}</p>

    <h2>Ventas por Mes</h2>
    <table>
        <thead>
            <tr>
                <th>Mes</th>
                <th>Ventas</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($ventasPorMes as $mes => $ventas)
                <tr>
                    <td>{{ $mes }}</td>
                    <td>${{ number_format($ventas, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>