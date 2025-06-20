<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function sendMessage(Request $request)
    {
        // 1. Validar el mensaje
        $request->validate([
            'message' => ['required', 'string', 'max:255'],
        ]);

        $message = $request->input('message');

        return redirect()->route('test-b')->with('message', $message);
    }
}