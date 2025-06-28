<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Models\ExchangeRate; // Asumiendo que tienes un modelo ExchangeRate

class BcvService
{
    // Las propiedades relacionadas con la API externa ya no son necesarias
    // protected $client;
    // protected $apiUrl = 'https://s3.amazonaws.com/dolartoday/data.json';
    protected $cacheKey = 'bcv_exchange_rate_manual'; // Nueva clave para la caché de la tasa manual
    protected $cacheTtl = 24 * 60 * 60; // 24 horas de caché para la tasa activa

    public function __construct()
    {
        // El cliente GuzzleHttp ya no es necesario
        // $this->client = new Client();
    }

    /**
     * Obtiene la tasa de cambio principal para la aplicación.
     * Prioriza la tasa guardada en base de datos/caché.
     */
    public function getExchangeRate()
    {
        // Intentar obtener la tasa de la caché primero
        $cachedRate = Cache::get($this->cacheKey);
        if ($cachedRate && is_numeric($cachedRate) && $cachedRate > 0) {
            return (float) $cachedRate;
        }

        // Si no está en caché, obtener la última tasa de la base de datos
        $lastDbRate = ExchangeRate::current(); // Asume que este método obtiene la última tasa activa
        if ($lastDbRate && is_numeric($lastDbRate->rate) && $lastDbRate->rate > 0) {
            $rate = (float) $lastDbRate->rate;
            Cache::put($this->cacheKey, $rate, $this->cacheTtl); // Guardar en caché
            return $rate;
        }

        // Fallback: si no hay tasa en DB ni caché, usar un valor predeterminado o lanzar excepción
        Log::warning("No se pudo obtener ninguna tasa de cambio. Volviendo al valor predeterminado (1.0).");
        return 1.0; // Valor predeterminado de emergencia (ej. si no hay registros o DB falló)
    }

    /**
     * Establece manualmente la tasa de cambio en la base de datos y actualiza la caché.
     * Esto requeriría una interfaz de administración para ser útil.
     * @param float $rate La nueva tasa a establecer.
     * @return bool True si se guardó correctamente, false si no.
     */
    public function setManualExchangeRate(float $rate)
    {
        if ($rate <= 0) {
            Log::warning("Intento de establecer una tasa de cambio no válida: " . $rate);
            return false;
        }

        try {
            // Guarda la nueva tasa en la base de datos
            // Puedes crear un nuevo registro o actualizar el existente como "activo"
            ExchangeRate::create([
                'rate' => $rate,
                'is_active' => true, // Puedes tener un campo para la tasa activa
                // Agrega otros campos si tu tabla los requiere (ej. 'date_set')
            ]);

            // Limpia la caché para que la nueva tasa sea leída de la DB inmediatamente
            Cache::forget($this->cacheKey);

            // Opcional: Si solo quieres mantener una tasa activa, desactiva las anteriores
            ExchangeRate::where('is_active', true)
                        ->where('rate', '!=', $rate) // Evita desactivar la que acabas de crear si el valor es el mismo
                        ->update(['is_active' => false]);

            return true;
        } catch (\Exception $e) {
            Log::error("Error al establecer la tasa de cambio manual: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Este método ya no es relevante si la API externa se elimina.
     * Podrías mantenerlo para limpiar la caché o eliminarlo.
     */
    public function refreshExchangeRate()
    {
        // Solo limpia la caché para forzar la lectura de la DB la próxima vez
        Cache::forget($this->cacheKey);
        return $this->getExchangeRate(); // Retorna la tasa actual de la DB
    }

    // El método getRawExchangeRate y propiedades relacionadas con Guzzle/API ya no son necesarios
    // protected function getRawExchangeRate() { /* ... */ }
}