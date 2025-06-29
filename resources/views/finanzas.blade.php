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
    transform: scale(1.02); /* Efecto de aumento ligero al pasar el ratón para no distorsionar demasiado el layout */
}

/* Encabezados de las tarjetas */
.card-header {
    padding: 1.5rem; /* Espaciado interno */
    font-size: 1.5rem; /* Tamaño de fuente del encabezado */
    background: linear-gradient(90deg,rgb(101, 40, 167),rgb(189, 133, 224));
    color: white; /* Color de texto blanco para el encabezado */
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

/* Clases para colores de borde en tarjetas de resumen */
.border-usd {
    border: 2px solid #007bff; /* Azul para USD */
}
.border-bs {
    border: 2px solid #28a745; /* Verde para Bs */
}
</style>

@section('content')
<div class="container my-5">
    <h1 class="text-center text-primary mb-4">Finanzas</h1>

    <div class="row mb-5">
        <!-- Tasa BCV Actual -->
        <div class="col-12 mb-4">
            <div class="card shadow-lg border-light rounded">
                <div class="card-header bg-info text-white text-center">
                    <h2 class="mb-0">Tasa de Cambio Actual</h2>
                </div>
                <div class="card-body text-center">
                    @if ($bcvRate > 0)
                        <p class="display-4 font-weight-bold">
                            1 USD = <span class="text-primary">{{ number_format($bcvRate, 4, ',', '.') }}</span> Bs
                        </p>
                        <p class="text-muted">Esta tasa es utilizada para los cálculos en Bolívares.</p>
                    @else
                        <p class="display-4 font-weight-bold text-danger">Tasa BCV no disponible.</p>
                        <p class="text-muted">Los cálculos en Bolívares pueden no ser precisos.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Resumen Financiero en USD -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-lg rounded border-usd">
                <div class="card-header text-white text-center">
                    <h2 class="mb-0">Resumen Financiero (USD)</h2>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <p class="font-weight-bold">Ingresos Totales:</p>
                        <p class="text-success display-4">${{ number_format($ingresosTotalesUSD ?? 0, 2, ',', '.') }}</p>
                    </div>
                    <div class="mb-4">
                        <p class="font-weight-bold">Gastos Totales:</p>
                        <p class="text-danger display-4">${{ number_format($gastosTotalesUSD ?? 0, 2, ',', '.') }}</p>
                    </div>
                    <div class="mb-4">
                        <p class="font-weight-bold">Beneficio Neto:</p>
                        <p class="text-success font-weight-bold display-4">${{ number_format($beneficioNetoUSD ?? 0, 2, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resumen Financiero en Bolívares -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-lg rounded border-bs">
                <div class="card-header text-white text-center" style="background: linear-gradient(90deg, #28a745, #66bb6a);">
                    <h2 class="mb-0">Resumen Financiero (Bs)</h2>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <p class="font-weight-bold">Ingresos Totales:</p>
                        <p class="text-success display-4">{{ number_format($ingresosTotalesBs ?? 0, 2, ',', '.') }} Bs</p>
                    </div>
                    <div class="mb-4">
                        <p class="font-weight-bold">Gastos Totales:</p>
                        <p class="text-danger display-4">{{ number_format($gastosTotalesBs ?? 0, 2, ',', '.') }} Bs</p>
                    </div>
                    <div class="mb-4">
                        <p class="font-weight-bold">Beneficio Neto:</p>
                        <p class="text-success font-weight-bold display-4">{{ number_format($beneficioNetoBs ?? 0, 2, ',', '.') }} Bs</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Gráfico de Ingresos y Gastos por Mes (USD) -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-lg border-light rounded">
                <div class="card-header text-white text-center">
                    <h2 class="mb-0">Ingresos y Gastos por Mes (USD)</h2>
                </div>
                <div class="card-body">
                    <canvas id="finanzasChartUSD"></canvas>
                </div>
            </div>
        </div>

        <!-- Gráfico de Ingresos y Gastos por Mes (Bs) -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-lg border-light rounded">
                <div class="card-header text-white text-center" style="background: linear-gradient(90deg, #28a745, #66bb6a);">
                    <h2 class="mb-0">Ingresos y Gastos por Mes (Bs)</h2>
                </div>
                <div class="card-body">
                    <canvas id="finanzasChartBs"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Gráfico de Beneficio Neto por Mes (USD) -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-lg border-light rounded">
                <div class="card-header text-white text-center">
                    <h2 class="mb-0">Beneficio Neto por Mes (USD)</h2>
                </div>
                <div class="card-body">
                    <canvas id="beneficioChartUSD"></canvas>
                </div>
            </div>
        </div>

        <!-- Gráfico de Beneficio Neto por Mes (Bs) -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-lg border-light rounded">
                <div class="card-header text-white text-center" style="background: linear-gradient(90deg, #28a745, #66bb6a);">
                    <h2 class="mb-0">Beneficio Neto por Mes (Bs)</h2>
                </div>
                <div class="card-body">
                    <canvas id="beneficioChartBs"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const labelsMeses = {{ Js::from($labelsMeses ?? []) }};
        const bcvRate = {{ Js::from($bcvRate ?? 0) }}; // Obtener la tasa BCV desde Laravel

        // --- Gráfico de Ingresos y Gastos por Mes (USD) ---
        const ctxFinanzasUSD = document.getElementById('finanzasChartUSD').getContext('2d');
        new Chart(ctxFinanzasUSD, {
            type: 'bar',
            data: {
                labels: labelsMeses,
                datasets: [{
                    label: 'Ingresos (USD)',
                    data: {{ Js::from($ingresosData ?? []) }},
                    backgroundColor: 'rgba(75, 192, 192, 0.5)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Gastos (USD)',
                    data: {{ Js::from($gastosData ?? []) }},
                    backgroundColor: 'rgba(255, 99, 132, 0.5)',
                    borderColor: 'rgba(255, 99, 132, 1)',
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
                            text: 'Monto (USD)'
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
                        text: 'Ingresos y Gastos Mensuales en Dólares',
                        font: {
                            size: 16
                        }
                    }
                }
            }
        });

        // --- Gráfico de Ingresos y Gastos por Mes (Bs) ---
        const ctxFinanzasBs = document.getElementById('finanzasChartBs').getContext('2d');
        new Chart(ctxFinanzasBs, {
            type: 'bar',
            data: {
                labels: labelsMeses,
                datasets: [{
                    label: 'Ingresos (Bs)',
                    data: {{ Js::from($ingresosDataBs ?? []) }}, // Datos en Bs
                    backgroundColor: 'rgba(40, 167, 69, 0.5)', /* Verde más oscuro */
                    borderColor: 'rgba(40, 167, 69, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Gastos (Bs)',
                    data: {{ Js::from($gastosDataBs ?? []) }}, // Datos en Bs
                    backgroundColor: 'rgba(220, 53, 69, 0.5)', /* Rojo */
                    borderColor: 'rgba(220, 53, 69, 1)',
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
                            text: 'Monto (Bs)'
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
                        text: 'Ingresos y Gastos Mensuales en Bolívares',
                        font: {
                            size: 16
                        }
                    }
                }
            }
        });

        // --- Gráfico de Beneficio Neto por Mes (USD) ---
        const ctxBeneficioUSD = document.getElementById('beneficioChartUSD').getContext('2d');
        new Chart(ctxBeneficioUSD, {
            type: 'line',
            data: {
                labels: labelsMeses,
                datasets: [{
                    label: 'Beneficio Neto (USD)',
                    data: {{ Js::from($beneficioNetoData ?? []) }},
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
                            text: 'Monto (USD)'
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
                        text: 'Beneficio Neto Mensual en Dólares',
                        font: {
                            size: 16
                        }
                    }
                }
            }
        });

        // --- Gráfico de Beneficio Neto por Mes (Bs) ---
        const ctxBeneficioBs = document.getElementById('beneficioChartBs').getContext('2d');
        new Chart(ctxBenefificioBs, {
            type: 'line',
            data: {
                labels: labelsMeses,
                datasets: [{
                    label: 'Beneficio Neto (Bs)',
                    data: {{ Js::from($beneficioNetoDataBs ?? []) }}, // Datos en Bs
                    backgroundColor: 'rgba(76, 175, 80, 0.2)', /* Un verde diferente para el beneficio */
                    borderColor: 'rgba(76, 175, 80, 1)',
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
                            text: 'Monto (Bs)'
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
                        text: 'Beneficio Neto Mensual en Bolívares',
                        font: {
                            size: 16
                        }
                    }
                }
            }
        });
    });
</script>
@endsection