@extends('layouts.app') <!-- Asegúrate de tener un layout base -->

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
<div class="container mt-12 text-center">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="bg-white shadow-xl rounded-lg overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-500 to-purple-500 py-4 px-6 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-white">{{ __('Actualizar Información') }}</h2>
                </div>
                <div class="p-8">
                    <form action="{{ route('update_account') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="name" class="block text-gray-700 text-sm font-bold mb-2">{{ __('Nombre') }}</label>
                            <input type="text" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" id="name" name="name" value="{{ auth()->user()->name }}" required>
                        </div>
                        <div class="form-group">
                            <label for="email" class="block text-gray-700 text-sm font-bold mb-2">{{ __('Correo Electrónico') }}</label>
                            <input type="email" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" id="email" name="email" value="{{ auth()->user()->email }}" required>
                        </div>
                        <br>
                        <button type="submit" class="px-4 py-2 bg-gradient-to-r from-indigo-500 to-purple-500 text-white rounded-md hover:bg-gradient-to-r hover:from-indigo-600 hover:to-purple-600 w-full">{{ __('Actualizar Información') }}</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="bg-white shadow-xl rounded-lg overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-500 to-purple-500 py-4 px-6 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-white">{{ __('Cambiar Contraseña') }}</h2>
                </div>
                <div class="p-8">
                    <form action="{{ route('change_password') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="current_password" class="block text-gray-700 text-sm font-bold mb-2">{{ __('Contraseña Actual') }}</label>
                            <input type="password" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" id="current_password" name="current_password" required>
                        </div>
                        <div class="form-group">
                            <label for="new_password" class="block text-gray-700 text-sm font-bold mb-2">{{ __('Nueva Contraseña') }}</label>
                            <input type="password" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" id="new_password" name="new_password" required>
                        </div>
                        <div class="form-group">
                            <label for="new_password_confirmation" class="block text-gray-700 text-sm font-bold mb-2">{{ __('Confirmar Nueva Contraseña') }}</label>
                            <input type="password" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" id="new_password_confirmation" name="new_password_confirmation" required>
                        </div>
                        <br>
                        <button type="submit" class="px-4 py-2 bg-gradient-to-r from-indigo-500 to-purple-500 text-white rounded-md hover:bg-gradient-to-r hover:from-indigo-600 hover:to-purple-600 w-full">{{ __('Cambiar Contraseña') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection