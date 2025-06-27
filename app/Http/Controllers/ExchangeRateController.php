<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ExchangeRate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache; // To clear the BCV rate cache
use App\Services\BcvService; // Assuming you have this service from previous discussion

class ExchangeRateController extends Controller
{
    protected $bcvService;

    public function __construct(BcvService $bcvService)
    {
        $this->bcvService = $bcvService;
    }
    public function index()
    {
        // Get the current rate from the database (latest entry)
        $currentDbRate = ExchangeRate::current();

        // Get the BCV rate from the service (which uses its own cache)
        $currentBcvRate = $this->bcvService->getExchangeRate();

        // Get the history of exchange rates for the table
        $history = ExchangeRate::with('user') // Eager load the user who made the change
            ->latest() // Order by latest changes first
            ->paginate(10); // Paginate the results

        return view('exchange_rates', [ // This will be our Blade view
            'currentDbRate' => $currentDbRate ? $currentDbRate->rate : 0,
            'currentBcvRate' => $currentBcvRate,
            'history' => $history,
        ]);
    }
    public function updateManual(Request $request)
    {
        $request->validate([
            'new_rate' => 'required|numeric|min:0.0001',
        ]);

        // Create a new entry in the history table
        ExchangeRate::create([
            'rate' => $request->new_rate,
            'user_id' => Auth::id(),
            'source' => 'Manual',
        ]);
        Cache::put('current_active_exchange_rate', $request->new_rate, now()->addHours(24));
        Cache::forget('bcv_exchange_rate');


        return redirect()->back()->with('success', 'Tasa de cambio actualizada manualmente a ' . $request->new_rate . ' Bs/USD.');
    }
    public function updateFromBcv(Request $request)
    {
        try {
            $newRate = $this->bcvService->refreshExchangeRate();

            if ($newRate > 0) {
                // Store the updated rate in your history database
                ExchangeRate::create([
                    'rate' => $newRate,
                    'user_id' => Auth::id(),
                    'source' => 'BCV API',
                ]);
                // Also update the general active rate if you have one
                Cache::put('current_active_exchange_rate', $newRate, now()->addHours(24));

                return redirect()->back()->with('success', "Tasa BCV actualizada y registrada a: {$newRate} Bs/USD.");
            } else {
                return redirect()->back()->with('error', 'No se pudo obtener una tasa BCV válida de la fuente externa. Intente de nuevo.');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al intentar actualizar la tasa BCV: ' . $e->getMessage());
        }
    }
}
