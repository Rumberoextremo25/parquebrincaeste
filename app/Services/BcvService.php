<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Models\ExchangeRate; // Import ExchangeRate model

class BcvService
{
    protected $client;
    protected $apiUrl = 'https://s3.amazonaws.com/dolartoday/data.json'; // O la API que uses
    protected $cacheKey = 'bcv_exchange_rate';
    protected $activeRateCacheKey = 'current_active_exchange_rate'; // New key for the actively used rate
    protected $cacheTtl = 60 * 60; // 1 hora de caché (for BCV API fetch)
    protected $activeRateTtl = 24 * 60 * 60; // 24 horas de caché para la tasa activa

    public function __construct()
    {
        $this->client = new Client();
    }

    /**
     * Obtiene la tasa de cambio principal para la aplicación.
     * Prioriza la tasa guardada en base de datos/caché activa si existe y es reciente.
     */
    public function getExchangeRate()
    {
        // First, try to get the active rate from cache (e.g., manually set or latest BCV fetch)
        $activeRate = Cache::get($this->activeRateCacheKey);
        if ($activeRate && is_numeric($activeRate) && $activeRate > 0) {
            return (float) $activeRate;
        }

        // If no active rate, try to get from BCV service (which will refresh if cache expired)
        $bcvRate = Cache::remember($this->cacheKey, $this->cacheTtl, function () {
            return $this->getRawExchangeRate();
        });

        // If BCV rate is valid, store it as the active rate and return it
        if ($bcvRate && is_numeric($bcvRate) && $bcvRate > 0) {
            Cache::put($this->activeRateCacheKey, $bcvRate, $this->activeRateTtl);
            return (float) $bcvRate;
        }

        // Fallback: get the very last recorded rate from the database
        $lastDbRate = ExchangeRate::current();
        if ($lastDbRate) {
             return (float) $lastDbRate->rate;
        }

        // Ultimate fallback if nothing works
        Log::warning("Could not obtain any exchange rate. Returning default 0.");
        return 0; // Default to 0 or throw an exception based on your application's tolerance
    }

    /**
     * Refresca la tasa de cambio, forzando la obtención de un nuevo valor BCV
     * y actualizando la caché activa.
     * @return float|null La nueva tasa de cambio o null si hubo un error.
     */
    public function refreshExchangeRate()
    {
        Cache::forget($this->cacheKey); // Clear the BCV API specific cache
        try {
            $newRate = $this->getRawExchangeRate();
            // If the new rate is valid, update the active rate cache
            if ($newRate > 0) {
                Cache::put($this->activeRateCacheKey, $newRate, $this->activeRateTtl);
            }
            return $newRate;
        } catch (\Exception $e) {
            Log::error("Error al refrescar la tasa BCV: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Hace la petición real a la API externa para obtener la tasa de cambio.
     * @return float La tasa de cambio.
     * @throws \Exception Si no se puede obtener la tasa.
     */
    protected function getRawExchangeRate()
    {
        try {
            $response = $this->client->get($this->apiUrl, ['timeout' => 5]);
            $data = json_decode($response->getBody()->getContents(), true);

            $rate = $data['USD']['bcv'] ?? $data['USD']['transferencia'] ?? null;

            if (is_null($rate) || !is_numeric($rate) || $rate <= 0) {
                throw new \Exception("No se pudo obtener una tasa BCV válida de la API.");
            }

            return (float) $rate;
        } catch (\GuzzleHttp\Exception\ConnectException $e) {
            Log::error("Error de conexión al obtener la tasa BCV: " . $e->getMessage());
            throw new \Exception("No se pudo conectar al servicio de la tasa BCV.");
        } catch (\Exception $e) {
            Log::error("Error al parsear datos de la API de la tasa BCV: " . $e->getMessage());
            throw $e;
        }
    }
}