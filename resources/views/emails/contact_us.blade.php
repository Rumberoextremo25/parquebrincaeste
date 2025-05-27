<!DOCTYPE html>
<html>
<head>
    <title>Nuevo Mensaje de Contacto</title>
</head>
<body>
    <h1>Nuevo Mensaje de {{ $data['name'] }}</h1>
    <p><strong>Teléfono:</strong> {{ $data['phone'] }}</p>
    <p><strong>Email:</strong> {{ $data['email'] }}</p>
    <p><strong>Mensaje:</strong></p>
    <p>{{ $data['message'] }}</p>
</body>
</html>