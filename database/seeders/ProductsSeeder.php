<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProductsSeeder extends Seeder
{
    public function run()
    {
        $precio_brazalete = 6.00;
        $precio_calcetines = 7.50;

        $products = [];

        // Generar brazaletes por hora
        $products = array_merge($products, $this->generateBrazaletesByHour($precio_brazalete));

        // Brazalete "Baby Park"
        $products[] = $this->createProductData(
            'Brazalete Baby Park',
            'Todas las Horas',
            $precio_brazalete,
            'Pass Baby Park',
            true
        );

        // Generar calcetines por talla
        $products = array_merge($products, $this->generateSocksByTalla($precio_calcetines));

        // Insertar los productos en la base de datos
        foreach ($products as $product) {
            DB::table('products')->insert($product);
        }
    }

    /**
     * Helper para crear un array de datos de producto.
     */
    private function createProductData(
        string $name,
        string $description,
        float $price,
        string $category,
        bool  $onlyChildren = false,
    ): array {
        return [
            'name' => $name,
            'description' => $description,
            'price' => $price,
            'created_at' => now(),
            'updated_at' => now(),
            'category' => $category,
            'only_children' => $onlyChildren,
        ];
    }
    /**
     * Helper para generar datos de brazaletes por hora.
     */
    private function generateBrazaletesByHour(float $price): array
    {
        $brazaletes = [];
        $horas = [
            '11:00 AM a 12:00 M' => 'Azul',
            '12:00 M a 1:00 PM' => 'Amarillo',
            '1:00 PM a 2:00 PM' => 'Rojo',
            '2:00 PM a 3:00 PM' => 'Verde Manzana',
            '3:00 PM a 4:00 PM' => 'Naranja',
            '4:00 PM a 5:00 PM' => 'Morado',
            '5:00 PM a 6:00 PM' => 'Negro',
            '6:00 PM a 7:00 PM' => 'Vinotinto',
            '7:00 PM a 8:00 PM' => 'Azul Rey',
            '8:00 PM a 9:00 PM' => 'Azul Marino',
        ];

        foreach ($horas as $description => $color) {
            $brazaletes[] = $this->createProductData(
                "Brazalete $color",
                $description,
                $price,
                'Brazalete'
            );
        }

        return $brazaletes;
    }

    /**
     * Helper para generar datos de calcetines por talla.
     */
    private function generateSocksByTalla(float $price): array
    {
        $calcetines = [];
        $tallas = ['S', 'M', 'L', 'XL'];

        foreach ($tallas as $talla) {
            $calcetines[] = $this->createProductData(
                "Calcetín Talla $talla",
                "Calcetines cómodos y suaves, talla $talla.",
                $price,
                'Calcetines'
            );
        }

        return $calcetines;
    }
}
