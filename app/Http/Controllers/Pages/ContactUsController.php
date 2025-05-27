<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class ContactUsController extends Controller
{
    public function contact_us()
    {
        return Inertia::render('ContactUs/ContactUs');
    }

    public function save()
    {
        sleep(2);
        return to_route('home')->with(['success' => '¡Consulta recibida! Te responderemos lo antes posible.']);;
    }
}
