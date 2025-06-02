@extends('layouts.app') <!-- Asegúrate de tener un layout base -->

<style>
/* Estilos generales */
body {
    background-color: #f8f9fa; /* Fondo claro para el cuerpo */
    font-family: 'Arial', sans-serif; /* Fuente moderna */
}

/* Títulos */
h1 {
    font-size: 2.5rem; /* Tamaño grande para el título principal */
    font-weight: bold;
}

.card {
    border-radius: 10px; /* Bordes redondeados en las tarjetas */
    transition: transform 0.3s; /* Transición suave al pasar el ratón */
}

.card:hover {
    transform: scale(1.05); /* Efecto de aumento al pasar el ratón */
}

/* Encabezados de las tarjetas */
.card-header {
    padding: 1.5rem; /* Espaciado interno */
    font-size: 1.5rem; /* Tamaño de fuente del encabezado */
    background: linear-gradient(90deg,rgb(101, 40, 167),rgb(189, 133, 224));
}

/* Contenido de las tarjetas */
.card-body {
    padding: 2rem; /* Espaciado interno */
}

/* Estilo de los totales */
.font-weight-bold {
    font-weight: bold; /* Negrita para los textos importantes */
}

.display-4 {
    font-size: 2rem; /* Tamaño grande para los totales */
}

/* Colores de texto */
.text-success {
    color: #28a745; /* Verde para los ingresos y beneficios */
}

.text-danger {
    color: #dc3545; /* Rojo para los gastos */
}

/* Estilo del gráfico */
canvas {
    max-width: 100%; /* Asegura que el gráfico no exceda el ancho de su contenedor */
    height: auto; /* Mantiene la proporción del gráfico */
}
</style>

@section('content')  
<div class="container my-5">  
    <h1 class="text-center text-primary mb-4">Finanzas</h1>  
    
    <div class="row">  
        <!-- Resumen Financiero -->
        <div class="col-md-6">  
            <div class="card shadow-lg border-light rounded">  
                <div class="card-header bg-primary text-white text-center">  
                    <h2 class="mb-0">Resumen Financiero</h2>  
                </div>  
                <div class="card-body">  
                    <div class="mb-4">  
                        <p class="font-weight-bold">Ingresos Totales:</p>  
                        <p class="text-success display-4">${{ number_format($ingresosTotales, 2) }}</p>  
                    </div>  
                    <div class="mb-4">  
                        <p class="font-weight-bold">Gastos Totales:</p>  
                        <p class="text-danger display-4">${{ number_format($gastosTotales, 2) }}</p>  
                    </div>  
                    <div class="mb-4">  
                        <p class="font-weight-bold">Beneficio Neto:</p>  
                        <p class="text-success font-weight-bold display-4">${{ number_format($beneficioNeto, 2) }}</p>  
                    </div>  
                </div>  
            </div>  
        </div>  

        <!-- Gráficos Financieros -->
        <div class="col-md-6">  
            <div class="card shadow-lg border-light rounded">  
                <div class="card-header bg-success text-white text-center">  
                    <h2 class="mb-0">Gráficos Financieros</h2>  
                </div>  
                <div class="card-body">  
                    <canvas id="finanzasChart"></canvas>  
                </div>
                <div class="card-body">  
                    <canvas id="beneficioChart"></canvas>  
                </div>
            </div>  
        </div>
    </div>  
</div>  

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>  
<script>
    // Gráfico de Ventas por Mes
    const ctxVentas = document.getElementById('finanzasChart').getContext('2d');
    const finanzasChart = new Chart(ctxVentas, {
        type: 'bar',
        data: {
            labels: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Sept', 'Oct', 'Nov', 'Dic'],
            datasets: [{
                label: 'Ventas por Mes',
                data: [12000, 15000, 13000, 17000, 20000, 18000, 16000, 21000, 19000, 22000, 24000, 20000], // datos estáticos
                backgroundColor: 'rgba(75, 192, 192, 0.5)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
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
            }
        }
    });

    // Gráfico estático de Beneficio Neto por mes
    const ctxBeneficio = document.getElementById('beneficioChart').getContext('2d');
    const beneficioChart = new Chart(ctxBeneficio, {
        type: 'line',
        data: {
            labels: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Sept', 'Oct', 'Nov', 'Dic'],
            datasets: [{
                label: 'Beneficio Neto por Mes',
                data: [5000, 7000, 6000, 8000, 10000, 9000, 8500, 11000, 10500, 11500, 12500, 10000], // datos estáticos
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
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
            }
        }
    });
</script>  
@endsection