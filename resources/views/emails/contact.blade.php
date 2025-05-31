<!DOCTYPE html>
<html>
<head>
    <title>Nuevo mensaje de contacto</title>
</head>
<body>
    <h1>Tienes un nuevo mensaje de contacto</h1>
    <p><strong>Nombre:</strong> {{ $contactMessage->name }}</p>
    <p><strong>Teléfono:</strong> {{ $contactMessage->phone }}</p>
    <p><strong>Email:</strong> {{ $contactMessage->email }}</p>
    <p><strong>Asunto:</strong> {{ $contactMessage->subject }}</p>
    <p><strong>Mensaje:</strong></p>
    <p>{{ $contactMessage->message }}</p>
</body>
</html>