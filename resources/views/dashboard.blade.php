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
                    <p class="card-text text-muted font-weight-bold">
                        {{-- New users today data is not provided by the method --}}
                        0 hoy (0.00%)
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
@endsection