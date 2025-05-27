<!DOCTYPE html>
<html>
<head>
    <title>Ventas</title>
    <style>
        /* Estilos para el PDF */
    </style>
</head>
<body>
    <h1>Reporte de Ventas</h1>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio</th>
                <th>Fecha</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ventas as $venta)
                <tr>
                    <td>{{ $venta->id }}</td>
                    <td>{{ $venta->producto }}</td>
                    <td>{{ $venta->cantidad }}</td>
                    <td>{{ $venta->precio }}</td>
                    <td>{{ $venta->fecha }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>