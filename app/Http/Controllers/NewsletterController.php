<?php  

namespace App\Http\Controllers;  

use Illuminate\Http\Request;  
use App\Models\Subscriber;  
use Illuminate\Support\Facades\Validator;  
use App\Mail\NewsletterSubscribed; // Asegúrate de incluir el Mailable  
use Illuminate\Support\Facades\Mail; // Importa la clase Mail

class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        // Validar el email
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:subscribers,email',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Crear el suscriptor
        $subscriber = Subscriber::create([
            'email' => $request->email,
        ]);

        // Enviar el correo de confirmación
        Mail::to($subscriber->email)->send(new NewsletterSubscribed($subscriber->email));

        // Redirigir con un mensaje de éxito
        return redirect()->back()->with('success', '¡Gracias por suscribirte! Te hemos enviado un correo de confirmación.');
    }
}