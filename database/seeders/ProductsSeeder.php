<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductsSeeder extends Seeder
{
    public function run()
    {
        // Datos de ejemplo para los productos
        $products = [
            [
                'name' => 'Brazalete Azul',
                'description' => '11:00 AM a 12:00 M',
                'price' => 6.00,
                'stock' => 150,
                'created_at' => now(),
                'updated_at' => now(),
                'category' => 'Categoria 1',
            ],
            [
                'name' => 'Brazalete Amarillo',
                'description' => '12:00 M a 1:00 PM',
                'price' => 6.00,
                'stock' => 100,
                'created_at' => now(),
                'updated_at' => now(),
                'category' => 'Categoria 2',
            ],
            [
                'name' => 'Brazalete Rojo',
                'description' => '1:00 PM a 2:00 PM',
                'price' => 5.00,
                'stock' => 20,
                'created_at' => now(),
                'updated_at' => now(),
                'category' => 'Categoria 3',
            ],
            [
                'name' => 'Brazalete Verde Manzana',
                'description' => '2:00 PM a 3:00 PM',
                'price' => 6.00,
                'stock' => 200,
                'created_at' => now(),
                'updated_at' => now(),
                'category' => 'Categoria 4',
            ],
            [
                'name' => 'Brazalete Naranja',
                'description' => '3:00 PM a 4:00 PM',
                'price' => 6.00,
                'stock' => 200,
                'created_at' => now(),
                'updated_at' => now(),
                'category' => 'Categoria 5',
            ],
            [
                'name' => 'Brazalete Morado',
                'description' => '4:00 PM a 5:00 PM',
                'price' => 6.00,
                'stock' => 200,
                'created_at' => now(),
                'updated_at' => now(),
                'category' => 'Categoria 6',
            ],
            [
                'name' => 'Brazalete Negro',
                'description' => '5:00 PM a 6:00 PM',
                'price' => 6.00,
                'stock' => 200,
                'created_at' => now(),
                'updated_at' => now(),
                'category' => 'Categoria 7',
            ],
            [
                'name' => 'Brazalete Vinotinto',
                'description' => '6:00 PM a 7:00 PM',
                'price' => 6.00,
                'stock' => 200,
                'created_at' => now(),
                'updated_at' => now(),
                'category' => 'Categoria 8',
            ],
            [
                'name' => 'Brazalete Azul Rey',
                'description' => '7:00 PM a 8:00 PM',
                'price' => 6.00,
                'stock' => 200,
                'created_at' => now(),
                'updated_at' => now(),
                'category' => 'Categoria 9',
            ],
            [
                'name' => 'Brazalete Azul Marino',
                'description' => '8:00 PM a 9:00 PM',
                'price' => 6.00,
                'stock' => 200,
                'created_at' => now(),
                'updated_at' => now(),
                'category' => 'Categoria 10',
            ],
            [
                'name' => 'Brazalete Baby Park',
                'description' => 'Todas las Horas',
                'price' => 6.00,
                'stock' => 200,
                'created_at' => now(),
                'updated_at' => now(),
                'category' => 'Categoria 11',
            ],

        ];

        // Insertar los productos en la base de datos
        DB::table('products')->insert($products);
    }
}