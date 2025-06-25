<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Generalmente es true para solicitudes de perfil de usuario autenticado
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['string', 'max:255'],
            'email' => ['email', 'max:255', Rule::unique(User::class)->ignore($this->user()->id)],
            // ¡AÑADE ESTA LÍNEA PARA EL CAMPO PHONE!
            'phone' => ['nullable', 'string', 'max:20'], // Ejemplo: puede ser nulo, string, max 20 caracteres
            // 'phone' => ['required', 'string', 'max:20', 'regex:/^\+?[0-9]{7,15}$/'], // Si quieres que sea requerido y con formato específico
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'phone.string' => 'El teléfono debe ser una cadena de texto.',
            'phone.max' => 'El teléfono no debe exceder los :max caracteres.',
            // ... otros mensajes de error para name, email, etc.
        ];
    }
}
