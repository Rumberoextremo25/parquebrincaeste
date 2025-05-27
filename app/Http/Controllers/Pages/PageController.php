<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class PageController extends Controller
{
    public function home()
    {
        return Inertia::render('Home/Home', []);
    }

    public function privacy_policy()
    {
        return Inertia::render('PrivacyPolicy/PrivacyPolicy');
    }

    public function about_us()
    {
        return Inertia::render('AboutUs/AboutUs');
    }

    public function terms_of_service()
    {
        return Inertia::render('TermsOfService/TermsOfService');
    }

    public function faq()
    {
        return Inertia::render('Faq/Faq');
    }

    public function stand()
    {
        return Inertia::render('Stand/Stand');
    }

    public function package()
    {
        return Inertia::render('Package/Package');
    }

    public function promotion()
    {
        return Inertia::render('Promotion/Promotion');
    }

    public function checkout()
    {
        return Inertia::render('Checkout/Checkout');
    }
    public function showProduct($id)
    {
        $product = Product::find($id);
        return response()->json($product);
    }

    public function settings()
    {
        return view('settings');
    }

    public function updateSettings(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->back()->withErrors(['error' => 'Usuario no autenticado.']);
        }

        if (!$user instanceof \App\Models\User) {
            error_log('Error: $user no es una instancia de App\Models\User');
            return redirect()->back()->withErrors(['error' => 'No se pudo guardar la configuración.']);
        }

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => ['nullable', 'string', Password::min(8)->mixedCase()->numbers()->symbols()],
            'two_factor_pin' => ['required_if:two_factor,1', 'numeric', 'digits:4'],
            'dark_mode' => 'sometimes|boolean',
            'notifications' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user->email = $request->email;

        if ($request->password) {
            $user->password = Hash::make($request->password);
        }

        // Manejo de la autenticación en dos pasos
        if ($request->has('two_factor')) {
            $user->two_factor_enabled = true;
            $user->two_factor_pin = $this->generateTwoFactorPin(); // Genera un PIN de 4 dígitos
            $twoFactorPin = $request->input('two_factor_pin');

            if ($user->two_factor_pin != $twoFactorPin) {
                return redirect()->back()->withErrors(['two_factor_pin' => 'PIN de autenticación en dos pasos inválido.']);
            }
        } else {
            $user->two_factor_enabled = false;
            $user->two_factor_pin = null; // Limpiar el PIN si se desactiva
        }

        $user->dark_mode = $request->has('dark_mode');

        $user->save();

        return redirect()->route('settings')->with('success', __('Configuración actualizada con éxito.'));
    }

    /**
     * Genera un PIN de 4 dígitos para la autenticación en dos pasos.
     *
     * @return int
     */
    protected function generateTwoFactorPin()
    {
        return random_int(1000, 9999);
    }

    public function updateDarkMode(Request $request)
    {
        $user = auth()->user();
        $user->dark_mode = $request->input('dark_mode');
        $user->save();

        return response()->json(['message' => 'Modo oscuro actualizado']);
    }

    // Método para generar un secreto para la autenticación en dos pasos
    private function generateTwoFactorSecret()
    {
        return app('auth')->generateTwoFactorSecret(); // Asegúrate de tener la configuración correcta
    }
};
