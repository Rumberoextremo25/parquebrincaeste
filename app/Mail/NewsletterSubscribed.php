<?php  

namespace App\Mail;  

use Illuminate\Bus\Queueable;  
use Illuminate\Mail\Mailable;  
use Illuminate\Queue\SerializesModels;  

class NewsletterSubscribed extends Mailable  
{  
    use Queueable, SerializesModels;  

    public $email;  

    /**  
     * Create a new message instance.  
     *  
     * @param string $email  
     */  
    public function __construct($email)  
    {  
        $this->email = $email;  
    }  

    /**  
     * Build the message.  
     *  
     * @return $this  
     */  
    public function build()  
    {  
        return $this->subject('Gracias por suscribirte a nuestro boletín')  
                    ->view('emails.newsletter_subscribed');  
    }  
}