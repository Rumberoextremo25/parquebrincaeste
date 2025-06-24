<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response
     * @noinspection PhpUndefinedMethodInspection // Suprime la advertencia de Intelephense para user()
     */
    public function edit(Request $request): Response
    {
        // En este contexto, $request->user() ya es un objeto Authenticatable (típicamente App\Models\User)
        return Inertia::render('Profile/AccountDetails', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
        ]);
    }

    /**
     * Update the user's profile information.
     *
     * @param  \App\Http\Requests\ProfileUpdateRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     * @noinspection PhpUndefinedMethodInspection // Suprime la advertencia de Intelephense para user()
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        // Esta línea es la clave. Llenará el usuario con todos los datos validados
        // por ProfileUpdateRequest, incluyendo 'phone' si lo añadiste allí.
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save(); // Guarda los cambios en la base de datos.

        return Redirect::route('profile.edit');
    }

    /**
     * Almacena o actualiza la contraseña del usuario.
     * Maneja la petición POST desde el componente ChangePassword de React/Inertia.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     * @noinspection PhpUndefinedMethodInspection // Suprime la advertencia de Intelephense para user()
     */
    public function storeChangePassword(Request $request)
    {
        try {
            // 1. Validar los datos de entrada
            $request->validate([
                'current_password' => ['required', 'string', function ($attribute, $value, $fail) {
                    // Auth::user() también es un objeto Authenticatable, y es la forma más común
                    // de acceder al usuario autenticado dentro de los controladores.
                    if (!Hash::check($value, Auth::user()->password)) {
                        $fail(__('La contraseña actual no es correcta.'));
                    }
                }],
                'password' => 'required|string|min:8|confirmed', // 'confirmed' valida que 'password_confirmation' coincida
            ], [
                'current_password.required' => 'La contraseña actual es obligatoria.',
                'password.required' => 'La nueva contraseña es obligatoria.',
                'password.min' => 'La nueva contraseña debe tener al menos :min caracteres.',
                'password.confirmed' => 'La confirmación de la contraseña no coincide.',
            ]);

            // 2. Obtener el usuario autenticado
            // Aquí puedes usar $request->user() o Auth::user(), ambos funcionan.
            $user = $request->user(); // O Auth::user();

            // 3. Actualizar la contraseña
            $user->update([
                'password' => Hash::make($request->password), // Encriptar la nueva contraseña
            ]);

            // 4. Redireccionar de vuelta con un mensaje de éxito para Inertia
            return back()->with('success', '¡Contraseña actualizada correctamente!');
        } catch (ValidationException $e) {
            // Si la validación falla, Inertia lo captura automáticamente y expone los errores en el frontend.
            return back()->withErrors($e->errors());
        } catch (\Exception $e) {
            // Para cualquier otro error inesperado
            Log::error('Error al cambiar contraseña: ' . $e->getMessage(), ['user_id' => Auth::id()]);
            return back()->withErrors(['general' => 'Ocurrió un error inesperado. Por favor, inténtalo de nuevo.']);
        }
    }


    /**
     * Delete the user's account.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     * @noinspection PhpUndefinedMethodInspection // Suprime la advertencia de Intelephense para user()
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        // $request->user() es un objeto Authenticatable
        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function storeAccountDetails(Request $request)
    {
        $user = Auth::user();

        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
        ]);

        $user->name = $validatedData['name'];
        $user->phone = $validatedData['phone'];
        $user->email = $validatedData['email'];

        $user->save();

        return redirect()->back()->with('success', 'Detalles de cuenta actualizados exitosamente.');

    }
}
