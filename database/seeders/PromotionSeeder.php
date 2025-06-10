<?php

namespace Database\Seeders;

use App\Models\Promotion; // Asegúrate de importar tu modelo Promotion
use Illuminate\Database\Seeder;
use Carbon\Carbon; // Para trabajar fácilmente con fechas

class PromotionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Limpia la tabla antes de insertar (opcional, para evitar duplicados en cada ejecución)
        Promotion::truncate();

        // Promoción de porcentaje (15% de descuento)
        Promotion::create([
            'code' => 'VERANO15',
            'type' => 'percentage',
            'value' => 0.15, // 15%
            'starts_at' => Carbon::now()->subDays(7), // Empezó hace 7 días
            'expires_at' => Carbon::now()->addMonths(1), // Expira en 1 mes
            'usage_limit' => null, // Sin límite de uso
            'used_count' => 0,
            'is_active' => true,
        ]);

        // Promoción de monto fijo ($10 de descuento)
        Promotion::create([
            'code' => 'AHORRA10',
            'type' => 'fixed',
            'value' => 10.00, // $10 de descuento
            'starts_at' => Carbon::now()->subDays(10), // Empezó hace 10 días
            'expires_at' => Carbon::now()->addWeeks(2), // Expira en 2 semanas
            'usage_limit' => 50, // Límite de 50 usos
            'used_count' => 5, // Ya se ha usado 5 veces
            'is_active' => true,
        ]);

        // Promoción solo para un uso
        Promotion::create([
            'code' => 'PRIMERACOMPRA',
            'type' => 'percentage',
            'value' => 0.20, // 20%
            'starts_at' => Carbon::now()->subHour(),
            'expires_at' => Carbon::now()->addYears(1),
            'usage_limit' => 1, // Solo un uso
            'used_count' => 0,
            'is_active' => true,
        ]);

        // Promoción expirada (para probar la validación de fecha)
        Promotion::create([
            'code' => 'NAVIDAD2023',
            'type' => 'percentage',
            'value' => 0.10,
            'starts_at' => Carbon::parse('2023-12-01'),
            'expires_at' => Carbon::parse('2023-12-31'), // Expirada
            'usage_limit' => null,
            'used_count' => 0,
            'is_active' => true,
        ]);

        // Promoción inactiva
        Promotion::create([
            'code' => 'TESTINACTIVO',
            'type' => 'fixed',
            'value' => 5.00,
            'starts_at' => Carbon::now(),
            'expires_at' => Carbon::now()->addMonth(),
            'usage_limit' => null,
            'used_count' => 0,
            'is_active' => false, // Inactiva
        ]);

        $this->command->info('¡Códigos de promoción insertados con éxito!');
    }
}
