{{-- resources/views/dashboard.blade.php --}}

@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1 class="text-center mb-4">Dashboard</h1>
    
    <div class="row">
        <div class="col-6 col-md-3">
            <div class="card text-white bg-info mb-3 shadow-sm border-0 hover-shadow">
                <div class="card-header text-center font-weight-bold">Usuarios</div>
                <div class="card-body text-center">
                    <h5 class="card-title display-5">150</h5>
                    <p class="card-text text-success font-weight-bold">+20%</p>
                </div>
            </div>
        </div>
        
        <div class="col-6 col-md-3">
            <div class="card text-white bg-danger mb-3 shadow-sm border-0 hover-shadow">
                <div class="card-header text-center font-weight-bold">Visitantes</div>
                <div class="card-body text-center">
                    <h5 class="card-title display-5">400+</h5>
                    <p class="card-text text-success font-weight-bold">+5%</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <canvas id="visitorChart" height="250"></canvas>
        </div>
    </div>
</div>

<style>
/* General Styles */
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
}

.card-body {
  padding: 1rem; /* Reducido para tarjetas más pequeñas */
}

.card-title {
  font-size: 2rem; /* Reducido el tamaño del texto */
  margin-bottom: 0;
}

.card-text {
  font-size: 1rem; /* Reducido el tamaño del texto */
}

/* Color Themes */
.bg-warning {
  background-color: #ffc107 !important;
  color: #fff;
}

.bg-success {
  background-color: #28a745 !important;
  color: #fff;
}

.bg-danger {
  background-color: #dc3545 !important;
  color: #fff;
}

.bg-info {
  background-color: #17a2b8 !important;
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
  const ctx = document.getElementById('visitorChart').getContext('2d');
  const visitorChart = new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: ['Visitantes', 'Nuevos Usuarios'],
      datasets: [{
        label: 'Estadísticas',
        data: [400, 150],
        backgroundColor: [
          'rgba(75, 192, 192, 0.6)',
          'rgba(255, 99, 132, 0.6)',
        ],
        borderColor: [
          'rgba(75, 192, 192, 1)',
          'rgba(255, 99, 132, 1)',
        ],
        borderWidth: 2,
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false, // Permite que el gráfico se ajuste al contenedor
      animation: {
        animateScale: true,
        animateRotate: true,
      },
      plugins: {
        legend: {
          position: 'top',
        },
        title: {
          display: true,
          text: 'Distribución de Visitantes y Nuevos Usuarios'
        }
      }
    }
  });
</script>
@endsection