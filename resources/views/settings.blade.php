@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl w-full">
        <div class="text-center mb-10">
            <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight leading-tight">
                Configuración de la Cuenta
            </h1>
            <p class="mt-3 text-lg text-gray-500">
                Actualiza tu información personal, de seguridad y preferencias de notificaciones.
            </p>
        </div>

        <div class="bg-white p-8 rounded-2xl shadow-xl border border-gray-100 transform transition-all duration-300 hover:shadow-2xl">
            <form action="{{ route('settings.update') }}" method="POST" class="space-y-8">
                @csrf
                @method('PUT')

                <div>
                    <h2 class="text-2xl font-bold text-gray-800 mb-6 pb-4 border-b border-gray-200">
                        Información de Contacto
                    </h2>
                    <div class="space-y-5">
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                                Correo Electrónico
                            </label>
                            <div class="relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8m-2 4v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"></path></svg>
                                </div>
                                <input type="email" name="email" id="email"
                                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg placeholder-gray-400 text-gray-900 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition duration-150 ease-in-out"
                                    placeholder="tu@ejemplo.com" value="{{ old('email', auth()->user()->email) }}" required>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <h2 class="text-2xl font-bold text-gray-800 mb-6 pb-4 border-b border-gray-200">
                        Cambiar Contraseña
                    </h2>
                    <div class="space-y-5">
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                                Nueva Contraseña
                            </label>
                            <div class="relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                </div>
                                <input type="password" name="password" id="password"
                                    class="block w-full pl-10 pr-10 py-2 border border-gray-300 rounded-lg placeholder-gray-400 text-gray-900 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition duration-150 ease-in-out"
                                    placeholder="Dejar en blanco si no deseas cambiarla">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer" id="togglePassword">
                                    <svg class="h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" id="eyeIcon">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </div>
                            </div>
                            <p class="mt-2 text-sm text-gray-500">
                                Una contraseña segura contiene al menos 8 caracteres, mayúsculas, minúsculas, números y símbolos.
                            </p>
                        </div>
                    </div>
                </div>

                <div>
                    <h2 class="text-2xl font-bold text-gray-800 mb-6 pb-4 border-b border-gray-200">
                        Notificaciones del Navegador
                    </h2>
                    <div class="flex items-center justify-between py-2">
                        <div class="flex-1">
                            <p class="block text-base text-gray-700 font-medium">
                                Recibir notificaciones importantes
                            </p>
                            <p class="mt-1 text-sm text-gray-500">
                                Te enviaremos alertas directamente a tu navegador sobre actualizaciones o eventos importantes.
                            </p>
                        </div>
                        <div class="ml-4 flex-shrink-0">
                            <button type="button" id="enableNotifications"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-150 ease-in-out">
                                Activar Notificaciones
                            </button>
                            <button type="button" id="disableNotifications" style="display:none;"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                                Desactivar Notificaciones
                            </button>
                            <span id="notificationStatus" class="ml-3 text-sm text-gray-600"></span>
                        </div>
                    </div>
                </div>

                <div class="pt-6">
                    <button type="submit"
                        class="w-full flex justify-center py-3 px-5 border border-transparent rounded-lg shadow-md text-lg font-semibold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out transform hover:scale-105">
                        Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- Lógica de Notificaciones Pop-up (SweetAlert2) ---
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: '{{ session('success') }}',
                showConfirmButton: false,
                timer: 3000
            });
        @endif

        @if($errors->any())
            Swal.fire({
                icon: 'error',
                title: '¡Error!',
                html: `@foreach ($errors->all() as $error)
                        <p class="text-sm">{{ $error }}</p>
                       @endforeach`,
                showConfirmButton: true,
                confirmButtonText: 'Entendido'
            });
        @endif

        // --- Lógica de Notificaciones Push Web ---
        const enableNotificationsBtn = document.getElementById('enableNotifications');
        const disableNotificationsBtn = document.getElementById('disableNotifications');
        const notificationStatusSpan = document.getElementById('notificationStatus');

        function updateNotificationUI() {
            if (!('Notification' in window)) {
                notificationStatusSpan.textContent = 'Tu navegador no soporta notificaciones.';
                enableNotificationsBtn.style.display = 'none';
                disableNotificationsBtn.style.display = 'none';
                return;
            }

            const permission = Notification.permission;

            if (permission === 'granted') {
                notificationStatusSpan.textContent = 'Notificaciones: Activadas';
                enableNotificationsBtn.style.display = 'none';
                disableNotificationsBtn.style.display = 'inline-flex';
            } else if (permission === 'denied') {
                notificationStatusSpan.textContent = 'Notificaciones: Bloqueadas (cambiar en la configuración del navegador)';
                enableNotificationsBtn.style.display = 'none';
                disableNotificationsBtn.style.display = 'none';
            } else { // 'default'
                notificationStatusSpan.textContent = 'Notificaciones: Desactivadas';
                enableNotificationsBtn.style.display = 'inline-flex';
                disableNotificationsBtn.style.display = 'none';
            }
        }

        enableNotificationsBtn.addEventListener('click', async () => {
            const permission = await Notification.requestPermission();
            updateNotificationUI();

            if (permission === 'granted') {
                Swal.fire({
                    icon: 'success',
                    title: '¡Permiso Concedido!',
                    text: 'Ahora recibirás notificaciones en tu navegador.',
                    showConfirmButton: false,
                    timer: 2500
                });
            } else if (permission === 'denied') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Permiso Denegado',
                    text: 'No podremos enviarte notificaciones del navegador.',
                    showConfirmButton: true,
                    confirmButtonText: 'Entendido'
                });
            }
        });

        disableNotificationsBtn.addEventListener('click', () => {
            Swal.fire({
                title: '¿Desactivar Notificaciones?',
                text: "Dejarás de recibir alertas en tu navegador.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, desactivar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire('¡Desactivadas!', 'Para desactivarlas realmente, la lógica de desuscripción se implementaría aquí.', 'info');
                    enableNotificationsBtn.style.display = 'inline-flex';
                    disableNotificationsBtn.style.display = 'none';
                    notificationStatusSpan.textContent = 'Notificaciones: Desactivadas';
                }
            });
        });

        updateNotificationUI();

        // --- Lógica para mostrar/ocultar contraseña ---
        const passwordInput = document.getElementById('password');
        const togglePassword = document.getElementById('togglePassword');
        const eyeIcon = document.getElementById('eyeIcon'); // El SVG del ojo

        togglePassword.addEventListener('click', function () {
            // Alternar el tipo del input entre 'password' y 'text'
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);

            // Cambiar el ícono del ojo
            if (type === 'text') {
                // Ojo cerrado (visible)
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7 1.274-4.057 5.064-7 9.542-7a10.05 10.05 0 011.875.175M12 17a3 3 0 100-6 3 3 0 000 6z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.25 12V4.75a.25.25 0 00-.25-.25H7a.25.25 0 00-.25.25V12"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 12c.5 0 1 .5 1 1s-.5 1-1 1-1-.5-1-1 .5-1 1-1z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.25 12h-5.5m5.5 0v7.25a.25.25 0 01-.25.25H7a.25.25 0 01-.25-.25V12"></path>
                    <line x1="4" y1="4" x2="20" y2="20" stroke="currentColor" stroke-width="2" stroke-linecap="round"></line>
                `;
            } else {
                // Ojo abierto (oculto)
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                `;
            }
        });
    });
</script>
@endsection