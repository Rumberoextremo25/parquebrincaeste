<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ExchangeRate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache; // Para limpiar la caché

class ExchangeRateController extends Controller
{
    // Ya no necesitamos inyectar BcvService si no lo vamos a usar para obtener la tasa automáticamente
    // public function __construct(BcvService $bcvService)
    // {
    //     $this->bcvService = $bcvService;
    // }

    public function index()
    {
        // Obtener la tasa actual de la base de datos (última entrada)
        // Se asume que 'current()' es un scope en tu modelo ExchangeRate que obtiene la última tasa
        $currentDbRate = ExchangeRate::current();

        // Ya no obtenemos la tasa del BCV de forma automática aquí
        // $currentBcvRate = $this->bcvService->getExchangeRate(); // Esto se elimina

        // Obtener el historial de tasas de cambio para la tabla
        $history = ExchangeRate::with('user') // Cargar el usuario que hizo el cambio
            ->latest() // Ordenar por los cambios más recientes primero
            ->paginate(10); // Paginar los resultados

        return view('exchange_rates', [ // Esta será nuestra vista de Blade
            'currentDbRate' => $currentDbRate ? $currentDbRate->rate : 0,
            // 'currentBcvRate' ya no se pasa a la vista
            'history' => $history,
        ]);
    }

    public function updateManual(Request $request)
    {
        $request->validate([
            'new_rate' => 'required|numeric|min:0.0001',
        ]);

        // Crear una nueva entrada en la tabla de historial
        ExchangeRate::create([
            'rate' => $request->new_rate,
            'user_id' => Auth::id(),
            'source' => 'Manual', // Indicamos claramente que la fuente es manual
        ]);

        // Actualizar la caché de la tasa activa con el valor manual
        // Esta caché es la que probablemente se usa en otras partes de tu aplicación
        Cache::put('current_active_exchange_rate', $request->new_rate, now()->addHours(24));

        // Es buena práctica limpiar cualquier caché anterior de la tasa BCV si existiera,
        // para asegurar que solo la tasa manual sea la considerada.
        Cache::forget('bcv_exchange_rate'); // Si tenías una caché específica para la tasa BCV externa

        return redirect()->back()->with('success', 'Tasa de cambio actualizada manualmente a ' . number_format($request->new_rate, 4) . ' Bs/USD.');
    }

    // El método updateFromBcv se elimina completamente ya que no se desea la actualización automática.
    /*
    public function updateFromBcv(Request $request)
    {
        try {
            $newRate = $this->bcvService->refreshExchangeRate();

            if ($newRate > 0) {
                ExchangeRate::create([
                    'rate' => $newRate,
                    'user_id' => Auth::id(),
                    'source' => 'BCV API',
                ]);
                Cache::put('current_active_exchange_rate', $newRate, now()->addHours(24));

                return redirect()->back()->with('success', "Tasa BCV actualizada y registrada a: {$newRate} Bs/USD.");
            } else {
                return redirect()->back()->with('error', 'No se pudo obtener una tasa BCV válida de la fuente externa. Intente de nuevo.');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al intentar actualizar la tasa BCV: ' . $e->getMessage());
        }
    }
    */
}
