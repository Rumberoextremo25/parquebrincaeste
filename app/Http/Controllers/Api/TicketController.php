<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket; // Usar tu modelo Ticket
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    /**
     * Obtiene los tickets (órdenes) para el usuario autenticado.
     * Incluye los ítems del ticket y sus productos asociados.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function myTickets(Request $request)
    {
        // Asegúrate de que el usuario esté autenticado
        if (!Auth::check()) {
            return response()->json(['message' => 'No autenticado.'], 401);
        }

        // Obtener tickets (órdenes) para el usuario autenticado
        // Cargar eager (carga ansiosa) ticketItems y sus productos asociados
        $tickets = Ticket::where('user_id', Auth::id())
                        ->with(['ticketItems' => function($query) {
                            $query->with('product'); // Cargar el producto para cada ítem del ticket
                        }])
                        ->orderByDesc('created_at') // Ordenar por los más recientes primero
                        ->get();

        return response()->json($tickets);
    }

    /**
     * Obtiene los detalles de un ticket (orden) específico.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function ticketDetails(Request $request, $id)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'No autenticado.'], 401);
        }

        $ticket = Ticket::with(['ticketItems.product']) // Cargar ítems de ticket y productos anidados
                        ->where('user_id', Auth::id()) // Asegúrate de que el ticket pertenece al usuario
                        ->findOrFail($id); // Lanza 404 si no se encuentra

        return response()->json($ticket);
    }
}