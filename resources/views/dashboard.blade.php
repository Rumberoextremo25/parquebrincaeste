{{-- resources/views/dashboard.blade.php --}}

@extends('layouts.app')

@section('title', 'Panel de Control - Parque Brinca') {{-- Título más descriptivo --}}

@section('content')
    <div class="container py-4"> {{-- Usamos py-4 para padding vertical --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="display-4 mb-0 text-dark">📊 Panel de Control</h1>
            {{-- Puedes añadir botones de acción aquí, si los necesitas --}}
            {{-- <a href="#" class="btn btn-primary">Nuevo Reporte</a> --}}
        </div>

        <hr class="my-4 border-light-subtle"> {{-- Separador visual más suave --}}

        <h2 class="h4 mb-3 text-muted">Métricas Clave</h2>
        <div class="row">
            {{-- Tarjeta de Usuarios Registrados --}}
            <div class="col-sm-6 col-lg-4 mb-4">
                <div class="card h-100 shadow-lg border-0 bg-gradient-user text-white"> {{-- h-100 para altura igual, sombra más pronunciada, gradiente personalizado --}}
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div class="d-flex align-items-center mb-3">
                            <i class="bi bi-person-fill fs-2 me-3 text-white-75"></i> {{-- Icono de Bootstrap Icons --}}
                            <h5 class="card-title mb-0 fw-semibold">Usuarios Registrados</h5>
                        </div>
                        {{-- Asegúrate de que $TotalUsers se pasa desde el controlador con este nombre --}}
                        <p class="display-3 mb-0 fw-bold">{{ $TotalUsers ?? 0 }}</p> {{-- Tamaño de texto más grande --}}
                        <small class="text-white-50">Total de usuarios en el sistema</small>
                    </div>
                </div>
            </div>

            {{-- Tarjeta de Visitas Totales --}}
            <div class="col-sm-6 col-lg-4 mb-4">
                <div class="card h-100 shadow-lg border-0 bg-gradient-visits text-white">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div class="d-flex align-items-center mb-3">
                            <i class="bi bi-graph-up-arrow fs-2 me-3 text-white-75"></i> {{-- Icono de Bootstrap Icons --}}
                            <h5 class="card-title mb-0 fw-semibold">Visitas Totales</h5>
                        </div>
                        {{-- Asegúrate de que $TotalVisits se pasa desde el controlador con este nombre --}}
                        <p class="display-3 mb-0 fw-bold">{{ $TotalVisits ?? 0 }}</p>
                        <small class="text-white-50">Total de visitas registradas</small>
                    </div>
                </div>
            </div>

            {{-- Tarjeta de Suscriptores del Newsletter --}}
            <div class="col-sm-6 col-lg-4 mb-4">
                <div class="card h-100 shadow-lg border-0 bg-gradient-subscribers text-white">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div class="d-flex align-items-center mb-3">
                            <i class="bi bi-newspaper fs-2 me-3 text-white-75"></i> {{-- Icono de Bootstrap Icons --}}
                            <h5 class="card-title mb-0 fw-semibold">Suscriptores Newsletter</h5>
                        </div>
                        {{-- Asegúrate de que $TotalSubscribers se pasa desde el controlador con este nombre --}}
                        <p class="display-3 mb-0 fw-bold">{{ $TotalSubscribers ?? 0 }}</p>
                        <small class="text-white-50">Personas interesadas en tus novedades</small>
                    </div>
                </div>
            </div>
        </div> {{-- Fin de la fila de métricas --}}

        {{-- Aquí puedes añadir la tabla de suscriptores si también quieres mostrar la lista --}}
        {{-- Para la tabla, necesitarías que tu controlador pase la variable $subscribers con Subscriber::all() o paginate() --}}
        {{-- Ya te di el código para la tabla en respuestas anteriores, puedes pegarlo aquí si lo necesitas. --}}

    </div>

    <style>
        /* Estilos generales para el dashboard */
        body {
            background-color: #f0f2f5;
            /* Fondo más suave y moderno */
            font-family: 'Inter', sans-serif;
            /* Fuente moderna, si la tienes importada */
        }

        .container {
            max-width: 1300px;
            /* Un poco más ancho para dashboards grandes */
        }

        .display-4 {
            font-size: 2.5rem;
            font-weight: 700;
            /* Más negrita para el título principal */
            color: #343a40;
            /* Color de texto oscuro para contraste */
        }

        .text-muted {
            color: #6c757d !important;
            /* Color gris estándar para texto sutil */
        }

        .border-light-subtle {
            border-color: #e9ecef !important;
            /* Color de borde más claro para HR */
        }

        /* Estilos de las tarjetas de métricas */
        .card {
            border-radius: 1rem;
            /* Bordes más redondeados */
            overflow: hidden;
            /* Para que el gradiente se aplique bien */
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
            /* Sombra inicial suave */
        }

        .card:hover {
            transform: translateY(-8px);
            /* Efecto flotante más pronunciado */
            box-shadow: 0 1rem 2rem rgba(0, 0, 0, 0.2);
            /* Sombra al pasar el mouse */
        }

        .card-body {
            padding: 2rem;
            /* Más padding interno */
            position: relative;
            z-index: 1;
            /* Para asegurar que el contenido esté sobre el fondo */
        }

        .card-title {
            font-size: 1.25rem;
            /* Tamaño del título de la métrica */
            font-weight: 600;
            opacity: 0.95;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
            /* Sombra ligera al texto para visibilidad */
        }

        .display-3 {
            /* Clase para los números grandes de las métricas */
            font-size: 3.5rem;
            font-weight: 800;
            /* Más negrita */
            line-height: 1;
            margin-top: 1rem;
            margin-bottom: 0.5rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .text-white-75 {
            opacity: 0.75;
            /* Iconos un poco menos opacos que el texto principal */
        }

        .text-white-50 {
            opacity: 0.6;
            /* Texto pequeño más sutil */
        }

        /* Gradientes para las tarjetas (PERSONALIZA ESTOS COLORES A TU GUSTO) */
        .bg-gradient-user {
            background: linear-gradient(135deg, #6dd5ed, #2193b0);
            /* Azul claro a azul oscuro */
        }

        .bg-gradient-visits {
            background: linear-gradient(135deg, #ee9ca7, #ffdde1);
            /* Rosa a blanco rosado */
        }

        .bg-gradient-subscribers {
            background: linear-gradient(135deg, #c3a3ff, #8d73f1);
            /* Morado claro a morado oscuro */
        }

        /* Estilos de Iconos (requiere Bootstrap Icons CDN) */
        .bi {
            font-size: 2rem;
            line-height: 1;
        }

        /* Utilidades de Bootstrap (solo si no las tienes en tu CSS global o si usas una versión antigua) */
        .d-flex {
            display: flex !important;
        }

        .justify-content-between {
            justify-content: space-between !important;
        }

        .align-items-center {
            align-items: center !important;
        }

        .flex-column {
            flex-direction: column !important;
        }

        .me-3 {
            margin-right: 1rem !important;
        }

        .mb-0 {
            margin-bottom: 0 !important;
        }

        .fw-bold {
            font-weight: 700 !important;
        }

        .fw-semibold {
            font-weight: 600 !important;
        }
    </style>
@endsection