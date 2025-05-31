<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactMessageMail;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class ContactController extends Controller
{
    public function contact_us()
    {
        return Inertia::render('ContactUs/ContactUs');
    }

    public function save(Request $request)
    {
        // Validar los datos
        $validated = $request->validate([
            'name' => 'required|string',
            'phone' => 'nullable|string',
            'email' => 'required|email',
            'subject' => 'nullable|string',
            'message' => 'required|string',
        ]);

        // Crear el mensaje de contacto
        $contactMessage = ContactMessage::create($validated); // Asegúrate de que este modelo tenga las propiedades

        // Enviar el correo
        Mail::to('brincaesteservidor@gmail.com')->send(new ContactMessageMail($contactMessage));

        // Redirigir o devolver respuesta
        return redirect()->back()->with('success', 'Mensaje enviado con éxito.');
    }
}
