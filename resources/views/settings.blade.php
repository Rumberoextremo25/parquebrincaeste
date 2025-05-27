<!-- resources/views/settings.blade.php -->

@extends('layouts.app')

<style>
/* Estilos generales */
body {
    font-family: 'Roboto', sans-serif;
    background-color: #f5f5f5;
}

/* Estilos para el encabezado */
.bg-gradient-to-r {
    background-image: linear-gradient(to right, #4c51bf, #6b46c1);
}

/* Estilos para los botones */
.bg-gradient-to-r.from-indigo-500.to-purple-500 {
    background-image: linear-gradient(to right, #4c51bf, #6b46c1);
}

.bg-gradient-to-r.hover\:from-indigo-600.hover\:to-purple-600:hover {
    background-image: linear-gradient(to right, #434190, #5a31a4);
}

/* Estilos para los campos de entrada */
.focus\:ring-indigo-500.focus\:border-indigo-500 {
    --tw-ring-color: #4c51bf;
    border-color: #4c51bf;
}

/* Estilos para los iconos */
.text-gray-400 {
    color: #a0aec0;
}

/* Estilos para el modo oscuro */
.dark\:bg-gray-700 {
    background-color: #2d3748;
}

.dark\:border-gray-600 {
    border-color: #4a5568;
}

.dark\:text-gray-400 {
    color: #a0aec0;
}

.dark\:peer-focus\:ring-indigo-800 {
    --tw-ring-color: #553c9a;
}

.dark\:peer-checked\:bg-indigo-600 {
    background-color: #4c51bf;
}
</style>

@section('content')
<div class="container py-12">
    <div class="flex justify-center">
        <div class="w-full md:w-2/3 lg:w-1/2">
            <div class="bg-white shadow-xl rounded-lg overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-500 to-purple-500 py-4 px-6 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-white">{{ __('Configuración de tu Cuenta') }}</h2>
                </div>
                <div class="p-8">
                    <form action="{{ route('settings.update') }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div>
                            <label for="email" class="block text-gray-700 text-sm font-bold mb-2">{{ __('Correo Electrónico') }}</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path d="M3 4a2 2 0 00-2 2v1.161A7.001 7.001 0 009.455 17.839V20h1.09v-2.161A7.001 7.001 0 0019 7.161V6a2 2 0 00-2-2H3z" />
                                    </svg>
                                </div>
                                <input type="email" name="email" id="email" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md" placeholder="you@example.com" value="{{ old('email', auth()->user()->email) }}" required>
                            </div>
                        </div>

                        <div>
                            <label for="password" class="block text-gray-700 text-sm font-bold mb-2">{{ __('Nueva Contraseña') }}</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <input type="password" name="password" id="password" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md" placeholder="{{ __('Dejar en blanco si no deseas cambiarla') }}">
                            </div>
                            <p class="mt-2 text-sm text-gray-500">{{ __('Recomendación: Usa una contraseña segura.') }}</p>
                        </div>

                        <div class="border-t border-gray-200 pt-6">
                            <h4 class="text-lg font-semibold text-gray-700 mb-3">{{ __('Opciones de Seguridad') }}</h4>
                            <form action="{{ route('settings.update') }}" method="POST">
                                @csrf
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <label for="two_factor" class="inline-flex relative items-center cursor-pointer">
                                            <input type="checkbox" id="two_factor" name="two_factor" class="sr-only peer" {{ auth()->user()->two_factor_enabled ? 'checked' : '' }}>
                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 dark:peer-focus:ring-indigo-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-indigo-600"></div>
                                        </label>
                                        <label for="two_factor" class="ml-3 block text-sm text-gray-700">
                                            {{ __('Activar Autenticación en Dos Pasos') }}
                                        </label>
                                    </div>
                                </div>
                                <p class="mt-2 text-sm text-gray-500">{{ __('Aumenta la seguridad de tu cuenta.') }}</p>
                                <button type="submit" class="mt-4 px-4 py-2 bg-gradient-to-r from-indigo-500 to-purple-500 text-white rounded-md hover:bg-gradient-to-r hover:from-indigo-600 hover:to-purple-600">
                                    {{ __('Guardar Cambios') }}
                                </button>
                                <br>
                            </form>
                        </div>

                        <div class="border-t border-gray-200 pt-6">
                            <h4 class="text-lg font-semibold text-gray-700 mb-3">{{ __('Preferencias de Visualización') }}</h4>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <label for="dark_mode" class="inline-flex relative items-center cursor-pointer">
                                        <input type="checkbox" id="dark_mode" name="dark_mode" class="sr-only peer" {{ auth()->user()->dark_mode ? 'checked' : '' }}>
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 dark:peer-focus:ring-indigo-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-indigo-600"></div>
                                    </label>
                                    <label for="dark_mode" class="ml-3 block text-sm text-gray-700">
                                        {{ __('Modo Oscuro') }}
                                    </label>
                                </div>
                            </div>
                            <br>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="{{ asset('js/dark-mode.js') }}"></script>
@endsection