{{-- resources/views/dashboard.blade.php --}}

@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container mt-5">
    <h1 class="text-center mb-4">Dashboard</h1>

    <div class="row justify-content-center">
        <div class="col-6 col-md-3">
            <div class="card text-white bg-info mb-3 shadow-sm border-0 hover-shadow">
                <div class="card-header text-center font-weight-bold">Usuarios Registrados</div>
                <div class="card-body text-center">
                    <h5 class="card-title display-5">{{ $totalUsers ?? 0 }}</h5>
                    <p class="card-text {{ ($percentageChangeUsersRaw ?? 0) >= 0 ? 'text-success' : 'text-danger' }} font-weight-bold">
                        {{ $newUsersToday ?? 0 }} hoy ({{ $percentageChangeUsers ?? '0.00%' }})
                    </p>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card text-white bg-danger mb-3 shadow-sm border-0 hover-shadow">
                <div class="card-header text-center font-weight-bold">Visitas Totales</div>
                <div class="card-body text-center">
                    <h5 class="card-title display-5">{{ $totalVisits ?? 0 }}</h5>
                    <p class="card-text {{ ($percentageChangeVisitsRaw ?? 0) >= 0 ? 'text-success' : 'text-danger' }} font-weight-bold">
                        {{ $visitsToday ?? 0 }} hoy ({{ $percentageChangeVisits ?? '0.00%' }})
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center mt-4">
        <div class="col-12 col-md-8">
            <canvas id="visitorChart" height="250"></canvas>
        </div>
    </div>
</div>

<style>
/* ... (tu estilo CSS existente) ... */
.container {
  margin-top: 2rem;
}

.card {
  border: none;
  border-radius: 0.5rem;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  transition: box-shadow 0.3s ease;
}

.card:hover {
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
}

.card-header {
  background-color: transparent;
  border-bottom: none;
  font-weight: bold;
  padding-bottom: 0.5rem;
}

.card-body {
  padding: 1rem;
}

.card-title {
  font-size: 2.5rem;
  margin-bottom: 0.5rem;
}

.card-text {
  font-size: 1rem;
}

.text-success {
    color: #28a745 !important;
}

.text-danger {
    color: #dc3545 !important;
}

/* Color Themes (as originally provided) */
.bg-info {
  background-color: #17a2b8 !important;
  color: #fff;
}

.bg-danger {
  background-color: #dc3545 !important;
  color: #fff;
}

/* Hover Effect */
.hover-shadow:hover {
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
  transition: box-shadow 0.3s ease;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('visitorChart').getContext('2d');

        const totalVisits = {{ Js::from($totalVisits ?? 0) }};
        const totalUsers = {{ Js::from($totalUsers ?? 0) }};
        const visitsToday = {{ Js::from($visitsToday ?? 0) }};
        const newUsersToday = {{ Js::from($newUsersToday ?? 0) }};

        const visitorChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Visitas Totales', 'Usuarios Registrados', 'Visitas Hoy', 'Usuarios Nuevos Hoy'],
                datasets: [{
                    label: 'Estadísticas',
                    data: [totalVisits, totalUsers, visitsToday, newUsersToday],
                    backgroundColor: [
                        'rgba(75, 192, 192, 0.8)', // Color para Visitas Totales
                        'rgba(255, 99, 132, 0.8)', // Color para Usuarios Registrados
                        'rgba(54, 162, 235, 0.8)', // Color para Visitas Hoy
                        'rgba(255, 206, 86, 0.8)', // Color para Usuarios Nuevos Hoy
                    ],
                    borderColor: [
                        'rgba(75, 192, 192, 1)',
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                    ],
                    borderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    animateScale: true,
                    animateRotate: true,
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            font: {
                                size: 14
                            }
                        }
                    },
                    title: {
                        display: true,
                        text: 'Distribución de Visitas y Usuarios',
                        font: {
                            size: 18
                        }
                    }
                }
            }
        });
    });
</script>
@endsection