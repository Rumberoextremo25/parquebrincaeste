@extends('layouts.app') {{-- Ajusta esto a tu layout de administrador --}}

@section('title', 'Gestión de Tasa de Cambio')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-3xl font-bold mb-6 text-gray-800">Gestión de Tasa de Cambio</h1>

    {{-- Flash Messages --}}
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">¡Éxito!</strong>
            <span class="block sm:inline ml-2">{{ session('success') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Error:</strong>
            <span class="block sm:inline ml-2">{{ session('error') }}</span>
        </div>
    @endif

    {{-- Contenedor principal de las tarjetas, ajustado a una columna --}}
    <div class="grid grid-cols-1 gap-6 mb-8">
        {{-- Current Rate Display --}}
        <div class="bg-white shadow-md rounded-lg p-6">
            <h2 class="text-2xl font-semibold mb-4 text-gray-700">Tasa de Cambio Actual</h2>
            <p class="text-xl text-gray-800 mb-2">
                <strong>Valor Activo:</strong> <span class="font-bold text-blue-600">{{ number_format($currentDbRate, 4, ',', '.') }}</span> Bs/USD
            </p>
            <p class="text-md text-gray-600">
                (Esta es la tasa que está siendo usada actualmente en la aplicación.)
            </p>
        </div>

        {{-- El bloque de "Update from BCV API" ha sido eliminado intencionalmente,
             ya que solo quieres la actualización manual. --}}
    </div>

    {{-- Manual Rate Update Form --}}
    <div class="bg-white shadow-md rounded-lg p-6 mb-8">
        <h2 class="text-2xl font-semibold mb-4 text-gray-700">Actualización Manual de Tasa</h2>
        <form action="{{ route('exchange_rates.update_manual') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="new_rate" class="block text-gray-700 text-sm font-bold mb-2">Nueva Tasa (Bs/USD)</label>
                <input type="number" step="0.0001" id="new_rate" name="new_rate" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('new_rate') border-red-500 @enderror" placeholder="Ej: 36.50" required>
                @error('new_rate')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-md focus:outline-none focus:shadow-outline transition duration-150 ease-in-out">
                Establecer Tasa Manualmente
            </button>
        </form>
    </div>

    {{-- Exchange Rate History Table --}}
    <div class="bg-white shadow-md rounded-lg p-6">
        <h2 class="text-2xl font-semibold mb-4 text-gray-700">Historial de Tasas de Cambio</h2>
        @if($history->isEmpty())
            <p class="text-gray-600">No hay historial de tasas de cambio aún.</p>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-200 divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tasa (Bs/USD)
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Fuente
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Cambiado Por
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Fecha y Hora
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($history as $rate)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ number_format($rate->rate, 4, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    {{ $rate->source }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    {{ $rate->user ? $rate->user->name : 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    {{ $rate->created_at->format('d/m/Y H:i') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination Links --}}
            <div class="mt-4">
                {{ $history->links() }}
            </div>
        @endif
    </div>
</div>
@endsection