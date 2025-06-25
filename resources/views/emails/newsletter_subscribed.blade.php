<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>¡Bienvenido a Brinca Este!</title>
    <style>
        body {
            font-family: 'Time New Roman', sans-serif; /* Fuente más común para compatibilidad */
            margin: 0;
            padding: 0;
            background-color: #f4f4f4; /* Fondo suave para el cuerpo del email */
            color: #000000; /* Color de texto principal */
            line-height: 1.6;
        }
        .container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff; /* Fondo blanco para el contenido */
            padding: 20px;
            border-radius: 8px; /* Bordes ligeramente redondeados */
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Sombra sutil */
        }
        .header {
            background-color: #6a0dad; /* Un morado vibrante, ajusta a tu marca */
            padding: 20px;
            text-align: center;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
        }
        .header h1 {
            color: #ffffff;
            font-size: 28px;
            margin: 0;
            padding: 0;
            font-weight: bold;
        }
        .content {
            padding: 20px;
            text-align: left;
        }
        .content h2 {
            color: #6a0dad; /* Color de marca para subtítulos */
            font-size: 24px;
            margin-top: 0;
            margin-bottom: 15px;
        }
        .content p {
            margin-bottom: 15px;
            font-size: 16px;
            color: #555555;
        }
        .button-container {
            text-align: center;
            margin-top: 25px;
            color: #eeeeee;
            margin-bottom: 25px;
        }
        .button {
            display: inline-block;
            background-color: #8a2be2; /* Un morado más suave, ajusta a tu marca */
            color: #ffffff;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            padding: 20px;
            font-size: 12px;
            color: #999999;
            border-top: 1px solid #eeeeee; /* Línea separadora */
            margin-top: 20px;
        }
        .footer a {
            color: #6a0dad;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>¡Bienvenido a Brinca Este!</h1>
        </div>
        <div class="content">
            <h2>¡Gracias por unirte a la diversión!</h2>
            <p>¡Hola!</p>
            <p>Queremos darte la bienvenida oficial a la comunidad de **Brinca Este**. Has sido suscrito con éxito a nuestro boletín y estamos emocionados de tenerte con nosotros.</p>
            <p>Prepárate para recibir **beneficios exclusivos**, noticias de nuestras últimas atracciones, eventos especiales y ofertas que solo nuestros suscriptores podrán disfrutar. ¡Mantente atento a tu bandeja de entrada!</p>

            <div class="button-container">
                <a href="https://www.brincaeste.com" class="button">Visita nuestro sitio web</a>
            </div>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Brinca Este 2024 C.A. Todos los derechos reservados.</p>
            <p>
                <a href="https://www.brincaeste/privacy-policy">Política de privacidad</a> |
                </p>
        </div>
    </div>
</body>
</html>