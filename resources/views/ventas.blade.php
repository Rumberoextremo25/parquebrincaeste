@extends('layouts.app') <style>
/* Estilos generales */
body {
    background-color: #f0f4f8; /* Fondo claro */
    font-family: 'Arial', sans-serif; /* Fuente moderna */
}

/* Títulos */
h1 {
    font-size: 2.5rem; /* Tamaño grande para el título principal */
    font-weight: bold;
}

/* Estilos de tarjetas */
.card {
    border-radius: 10px; /* Bordes redondeados */
    transition: transform 0.3s; /* Transición suave al pasar el ratón */
}

.card:hover {
    transform: scale(1.05); /* Efecto de aumento al pasar el ratón */
}

/* Estilos de fondo degradante */
.bg-gradient-success {
    background: linear-gradient(90deg, #28a745, #85e0a0); /* Degradado verde */
}

.bg-gradient-primary {
    background: linear-gradient(90deg, #007bff, #66b3ff); /* Degradado azul */
}

.bg-gradient-warning {
    background: linear-gradient(90deg, #ffc107, #ffe8a1); /* Degradado amarillo */
}

.bg-gradient-info {
    background: linear-gradient(90deg, #17a2b8, #85e0e6); /* Degradado cian */
}

/* Estilos de texto */
.display-4 {
    font-size: 2rem; /* Tamaño grande para los totales */
}

/* Botones */
.btn {
    padding: 10px 20px; /* Espaciado interno */
    font-size: 1.2rem; /* Tamaño de fuente del botón */
    border-radius: 5px; /* Bordes redondeados */
}

/* Estilos para el formulario de filtro */
.filter-form {
    background-color: #ffffff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    margin-bottom: 30px;
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 15px; /* Espacio entre elementos */
    flex-wrap: wrap; /* Permite que los elementos se envuelvan en pantallas pequeñas */
}
.filter-form label {
    font-weight: bold;
    color: #555;
}
.filter-form input[type="date"] {
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 1rem;
    flex-grow: 1; /* Permite que los inputs crezcan */
    max-width: 200px; /* Ancho máximo para los inputs de fecha */
}
.filter-form button {
    /* Utiliza tus estilos .btn existentes o específicos */
    background-color: #28a745; /* Verde para el botón de filtrar */
    color: white;
    border: none;
    cursor: pointer;
    font-size: 1rem;
    padding: 10px 20px;
    border-radius: 5px;
    transition: background-color 0.3s;
}
.filter-form button:hover {
    background-color: #218838;
}

</style>

@section('content')
<div class="container mt-5">
    <div class="row text-center mt-4">
        <div class="col-md-12 text-right">
            {{-- EL BOTÓN "Ver Ventas en PDF" AHORA NECESITA EL FORMULARIO --}}
            {{-- Vamos a mover el botón dentro de un formulario de filtro --}}
        </div>
    </div>

    <h1 class="text-center mb-4 text-primary">Ventas</h1>

    {{-- FORMULARIO DE FILTRO DE FECHAS --}}
    <div class="filter-form">
        {{-- El action apunta a la misma ruta que genera el PDF --}}
        <form action="{{ route('ventas.pdf') }}" method="GET" class="d-flex flex-wrap justify-content-center align-items-center gap-3">
            <label for="from_date">Desde:</label>
            <input type="date" id="from_date" name="from_date"
                   value="{{ request('from_date', Carbon\Carbon::now()->startOfMonth()->format('Y-m-d')) }}"> {{-- Valor por defecto: inicio del mes --}}

            <label for="to_date">Hasta:</label>
            <input type="date" id="to_date" name="to_date"
                   value="{{ request('to_date', Carbon\Carbon::now()->format('Y-m-d')) }}"> {{-- Valor por defecto: hoy --}}

            <button type="submit" class="btn btn-primary btn-lg">Generar PDF con Filtro</button>
        </form>
    </div>
    {{-- FIN FORMULARIO DE FILTRO --}}

    <div class="row text-center mb-4">
        <div class="col-sm-6 col-md-4 col-lg-3">
            <div class="card shadow-lg border-light rounded">
                <div class="card-body bg-gradient-success text-white">
                    <h2 class="card-title">Ventas Diarias</h2>
                    <p class="card-text display-4">${{ number_format($ventasDiarias ?? 0, 2) }}</p>
                </div>
            </div>
        </div>
        </div>

    <div class="row">
        <div class="col-md-12">
            <h2 class="text-center mb-4">Gráficos de Ventas Mensuales</h2>
            <canvas id="ventasChart" class="w-100"></canvas>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var ctx = document.getElementById('ventasChart').getContext('2d');

        // Obtener datos dinámicos de las variables Blade
        const labelsMeses = {{ Js::from($labelsMeses ?? []) }};
        const ventasData = {{ Js::from($ventasData ?? []) }};

        var ventasChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labelsMeses, // Datos dinámicos de los meses
                datasets: [{
                    label: 'Ventas por Mes',
                    data: ventasData,
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 2,
                    fill: false
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Monto ($)'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Meses'
                        }
                    }
                },
                plugins: {
                    title: {
                        display: true,
                        text: 'Ventas Mensuales', // Título del gráfico
                        font: {
                            size: 18
                        }
                    },
                    legend: {
                        display: true,
                        position: 'top'
                    }
                }
            }
        });
    });
</script>
@endsection