<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FinanzasSeeder extends Seeder
{
    public function run()
    {
        // Insertar datos de ejemplo en la tabla finanzas
        DB::table('finanzas')->insert([
            [
                'ingreso' => 100.00,
                'gasto' => 0.00, // Puedes ajustar el gasto según sea necesario
                'fecha' => now()->subDays(10), // Fecha de hace 10 días
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ingreso' => 250.50,
                'gasto' => 0.00, // Puedes ajustar el gasto según sea necesario
                'fecha' => now()->subDays(5), // Fecha de hace 5 días
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ingreso' => 0.00, // Ejemplo de gasto
                'gasto' => 75.75,
                'fecha' => now()->subDays(3), // Fecha de hace 3 días
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Añade más datos según sea necesario
        ]);
    }
}
