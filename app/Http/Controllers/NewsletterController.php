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
        // Validar la solicitud  
        $validator = Validator::make($request->all(), [  
            'email' => 'required|email|unique:subscribers,email',  
        ]);  

        if ($validator->fails()) {  
            return response()->json([  
                'message' => $validator->errors()->first(),  
                'success' => false,  
            ], 422);  
        }  

        // Crear el nuevo suscriptor  
        $subscriber = Subscriber::create(['email' => $request->email]);
        
        // Añade esto justo antes del envío del correo en el NewsletterController  
        dd('Listo para enviar el correo a: '.$subscriber->email);

        // Enviar el correo al nuevo suscriptor  
        Mail::to($subscriber->email)->send(new NewsletterSubscribed($subscriber->email));  

        return response()->json([  
            'message' => '¡Gracias por suscribirte!',  
            'success' => true,  
        ]);  
    }  
}