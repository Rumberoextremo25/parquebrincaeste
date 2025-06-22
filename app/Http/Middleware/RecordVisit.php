<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Visita; // Importa tu modelo Visita
use Carbon\Carbon; // Importa Carbon
use Illuminate\Support\Facades\Auth; // Para acceder al usuario autenticado
use Symfony\Component\HttpFoundation\Response; // Importa Response

class RecordVisit
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verifica si la visita ya ha sido registrada en esta sesión para evitar duplicados en la misma carga de página.
        // Esto es útil si el middleware se aplica a varias rutas que podrían ser llamadas dentro de una sola "carga de página" compleja.
        // Para una simple carga de página, una única creación debería ser suficiente.
        // if (!$request->session()->has('visit_recorded')) {

            Visita::create([
                'ip' => $request->ip(),
                'user_id' => Auth::check() ? Auth::id() : null,
                'user_agent' => $request->header('User-Agent'),
                'url_visitada' => $request->fullUrl(),
                'referrer' => $request->headers->get('referer'),
                'session_id' => $request->session()->getId(),
                // 'pais' => 'Venezuela', // Requiere lógica adicional para inferir el país
                'created_at' => Carbon::now(),
            ]);

            // Opcional: marca la sesión para evitar múltiples registros en la misma solicitud/sesión muy corta
            // $request->session()->put('visit_recorded', true);
        // }

        return $next($request); // Continúa con la solicitud normal
    }
}
