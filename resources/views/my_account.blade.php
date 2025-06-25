@extends('layouts.app') <!-- Asegúrate de tener un layout base -->

<head>
    <!-- Incluye Font Awesome para los iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" xintegrity="sha512-Fo3rlroBq5+Y4X9B0S/G0t8W/R5+6I4n1D/k6gV7/q8t+s2e8T/o5eXj6o/f+jC8f/2l+vP+w6f+u/t+g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<style>
/* Estilos generales */
body {
    font-family: 'Inter', sans-serif; /* Cambiado a Inter para consistencia */
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

/* Estilos para el contenedor de la contraseña */
.password-input-container {
    position: relative;
}

.password-toggle {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    color: #a0aec0; /* Tailwind text-gray-400 */
    padding: 5px;
    border-radius: 50%;
    transition: background-color 0.2s ease;
}

.password-toggle:hover {
    background-color: #edf2f7; /* Tailwind bg-gray-100 */
}
</style>
@section('content')
<div class="min-h-screen bg-gray-50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 font-inter">
    <div class="max-w-4xl w-full space-y-10">
        <div class="text-center">
            <h2 class="text-4xl font-extrabold text-gray-900 leading-tight">
                Gestiona tu Perfil
            </h2>
            <p class="mt-3 text-lg text-gray-500">
                Mantén tu información personal y seguridad al día.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
            <div class="bg-white p-10 rounded-2xl shadow-xl border border-gray-100 transform transition-all duration-300 hover:shadow-2xl hover:-translate-y-1">
                <div class="mb-8">
                    <h3 class="text-2xl font-bold text-gray-800 border-b pb-4 border-gray-200">
                        Actualizar Información
                    </h3>
                </div>
                <form action="{{ route('update_account') }}" method="POST" class="space-y-6">
                    @csrf
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                        <input type="text" id="name" name="name" value="{{ auth()->user()->name }}" required
                            class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition duration-150 ease-in-out">
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Correo Electrónico</label>
                        <input type="email" id="email" name="email" value="{{ auth()->user()->email }}" required
                            class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition duration-150 ease-in-out">
                    </div>
                    <button type="submit"
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-md text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out transform hover:scale-105">
                        Guardar Cambios
                    </button>
                </form>
            </div>

            <div class="bg-white p-10 rounded-2xl shadow-xl border border-gray-100 transform transition-all duration-300 hover:shadow-2xl hover:-translate-y-1">
                <div class="mb-8">
                    <h3 class="text-2xl font-bold text-gray-800 border-b pb-4 border-gray-200">
                        Cambiar Contraseña
                    </h3>
                </div>
                <form action="{{ route('change_password') }}" method="POST" class="space-y-6">
                    @csrf
                    <!-- Contraseña Actual -->
                    <div>
                        <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">Contraseña Actual</label>
                        <div class="password-input-container">
                            <input type="password" id="current_password" name="current_password" required
                                class="appearance-none block w-full pr-10 px-4 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition duration-150 ease-in-out">
                            <i class="fa-solid fa-eye password-toggle" onclick="togglePasswordVisibility('current_password')"></i>
                        </div>
                    </div>
                    <!-- Nueva Contraseña -->
                    <div>
                        <label for="new_password" class="block text-sm font-medium text-gray-700 mb-1">Nueva Contraseña</label>
                        <div class="password-input-container">
                            <input type="password" id="new_password" name="new_password" required
                                class="appearance-none block w-full pr-10 px-4 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition duration-150 ease-in-out">
                            <i class="fa-solid fa-eye password-toggle" onclick="togglePasswordVisibility('new_password')"></i>
                        </div>
                    </div>
                    <!-- Confirmar Nueva Contraseña -->
                    <div>
                        <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirmar Nueva Contraseña</label>
                        <div class="password-input-container">
                            <input type="password" id="new_password_confirmation" name="new_password_confirmation" required
                                class="appearance-none block w-full pr-10 px-4 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition duration-150 ease-in-out">
                            <i class="fa-solid fa-eye password-toggle" onclick="togglePasswordVisibility('new_password_confirmation')"></i>
                        </div>
                    </div>
                    <button type="submit"
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-md text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out transform hover:scale-105">
                        Actualizar Contraseña
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Función para alternar la visibilidad de la contraseña
    function togglePasswordVisibility(id) {
        const passwordInput = document.getElementById(id);
        const toggleIcon = passwordInput.nextElementSibling; // El icono es el siguiente hermano del input

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleIcon.classList.remove('fa-eye');
            toggleIcon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            toggleIcon.classList.remove('fa-eye-slash');
            toggleIcon.classList.add('fa-eye');
        }
    }
</script>
@endsection