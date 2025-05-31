<?php

namespace App\Mail;

use App\Models\ContactMessage; // Asegúrate de importar el modelo correcto
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactMessageMail extends Mailable
{
    use Queueable, SerializesModels;

    public $contactMessage; // Cambia el nombre de la propiedad a algo más representativo

    public function __construct(ContactMessage $contactMessage)
    {
        $this->contactMessage = $contactMessage; // Almacena el objeto en la propiedad
    }

    public function build()
    {
        return $this->view('emails.contact') // Asegúrate de que la vista exista
                    ->subject('Nuevo mensaje de contacto'); // Puedes personalizar el asunto
    }
}
