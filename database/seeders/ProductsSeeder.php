<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProductsSeeder extends Seeder
{
    public function run()
    {
        // Obtener el día de la semana actual (0 = Domingo, 1 = Lunes, ..., 6 = Sábado)
        $currentDay = Carbon::now()->dayOfWeek;

        // Establecer el precio de los brazaletes según el día
        $brazaletesPrice = ($currentDay == 2 || $currentDay == 3) ? 5.00 : 6.00;

        // Establecer el precio de los calcetines
        $calcetinesPrice = $brazaletesPrice + 1.50;

        $products = [
            [
                'name' => 'Brazalete Azul',
                'description' => '11:00 AM a 12:00 M',
                'price' => $brazaletesPrice,
                'stock' => 150,
                'created_at' => now(),
                'updated_at' => now(),
                'category' => 'Azul',
            ],
            [
                'name' => 'Brazalete Amarillo',
                'description' => '12:00 M a 1:00 PM',
                'price' => $brazaletesPrice,
                'stock' => 100,
                'created_at' => now(),
                'updated_at' => now(),
                'category' => 'Amarillo',
            ],
            [
                'name' => 'Brazalete Rojo',
                'description' => '1:00 PM a 2:00 PM',
                'price' => $brazaletesPrice,
                'stock' => 20,
                'created_at' => now(),
                'updated_at' => now(),
                'category' => 'Rojo',
            ],
            [
                'name' => 'Brazalete Verde Manzana',
                'description' => '2:00 PM a 3:00 PM',
                'price' => $brazaletesPrice,
                'stock' => 200,
                'created_at' => now(),
                'updated_at' => now(),
                'category' => 'Manzana',
            ],
            [
                'name' => 'Brazalete Naranja',
                'description' => '3:00 PM a 4:00 PM',
                'price' => $brazaletesPrice,
                'stock' => 200,
                'created_at' => now(),
                'updated_at' => now(),
                'category' => 'Naranja',
            ],
            [
                'name' => 'Brazalete Morado',
                'description' => '4:00 PM a 5:00 PM',
                'price' => $brazaletesPrice,
                'stock' => 200,
                'created_at' => now(),
                'updated_at' => now(),
                'category' => 'Morado',
            ],
            [
                'name' => 'Brazalete Negro',
                'description' => '5:00 PM a 6:00 PM',
                'price' => $brazaletesPrice,
                'stock' => 200,
                'created_at' => now(),
                'updated_at' => now(),
                'category' => 'Negro',
            ],
            [
                'name' => 'Brazalete Vinotinto',
                'description' => '6:00 PM a 7:00 PM',
                'price' => $brazaletesPrice,
                'stock' => 200,
                'created_at' => now(),
                'updated_at' => now(),
                'category' => 'Vinotinto',
            ],
            [
                'name' => 'Brazalete Azul Rey',
                'description' => '7:00 PM a 8:00 PM',
                'price' => $brazaletesPrice,
                'stock' => 200,
                'created_at' => now(),
                'updated_at' => now(),
                'category' => 'Rey',
            ],
            [
                'name' => 'Brazalete Azul Marino',
                'description' => '8:00 PM a 9:00 PM',
                'price' => $brazaletesPrice,
                'stock' => 200,
                'created_at' => now(),
                'updated_at' => now(),
                'category' => 'Marino',
            ],
            [
                'name' => 'Brazalete Baby Park',
                'description' => 'Todas las Horas',
                'price' => $brazaletesPrice,
                'stock' => 200,
                'created_at' => now(),
                'updated_at' => now(),
                'category' => 'Baby Park',
            ],
            // Productos de calcetines
            [
                'name' => 'Calcetín Talla S',
                'description' => 'Calcetines cómodos y suaves, talla S.',
                'price' => $calcetinesPrice,
                'stock' => 100,
                'created_at' => now(),
                'updated_at' => now(),
                'category' => 'Calcetines',
            ],
            [
                'name' => 'Calcetín Talla M',
                'description' => 'Calcetines cómodos y suaves, talla M.',
                'price' => $calcetinesPrice,
                'stock' => 100,
                'created_at' => now(),
                'updated_at' => now(),
                'category' => 'Calcetines',
            ],
            [
                'name' => 'Calcetín Talla L',
                'description' => 'Calcetines cómodos y suaves, talla L.',
                'price' => $calcetinesPrice,
                'stock' => 100,
                'created_at' => now(),
                'updated_at' => now(),
                'category' => 'Calcetines',
            ],
            [
                'name' => 'Calcetín Talla XL',
                'description' => 'Calcetines cómodos y suaves, talla XL.',
                'price' => $calcetinesPrice,
                'stock' => 100,
                'created_at' => now(),
                'updated_at' => now(),
                'category' => 'Calcetines',
            ],
        ];

        // Insertar los productos en la base de datos
        foreach ($products as $product) {
            DB::table('products')->insert($product);
        }
    }
}