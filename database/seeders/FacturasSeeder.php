<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as FakerFactory;

class FacturasSeeder extends Seeder
{
    public function run()
    {
        $faker = FakerFactory::create();

        for ($i = 0; $i < 30; $i++) {
            DB::table('facturas')->insert([
                'nombre_completo' => $faker->name,
                'correo' => $faker->unique()->safeEmail,
                'telefono' => $faker->phoneNumber,
                'direccion' => $faker->address,
                'ciudad' => $faker->city,
                'codigo_postal' => $faker->postcode,
                'promoCode' => $faker->randomElement([null, 'PROMO10', 'DESCUENTO15', 'FREESHIP']),
                'paymentMethod' => $faker->randomElement(['Tarjeta de Crédito', 'PayPal', 'transferencia']),
                'monto' => $faker->randomFloat(2, 50, 5000),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}