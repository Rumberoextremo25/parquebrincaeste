<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductsSeeder extends Seeder
{
    public function run()
    {
        // Precios definidos para brazaletes
        $precio_brazalete_semana = 5.00; // Martes a Viernes
        $precio_brazalete_fin_semana = 6.00; // Sabado a Domingo
        $precio_calcetines = 1.50; // ¡Costo actualizado para las medias!

        $products = [];

        // Generar brazaletes por hora para Días de Semana (Martes a Jueves)
        $products = array_merge($products, $this->generateBrazaletesByHour(
            $precio_brazalete_semana,
            ' (Martes a Viernes)' // Sufijo para diferenciar
        ));

        // Generar brazaletes por hora para Fines de Semana (Viernes a Domingo)
        $products = array_merge($products, $this->generateBrazaletesByHour(
            $precio_brazalete_fin_semana,
            ' (Sabado a Domingo)' // Sufijo para diferenciar
        ));

        // Brazalete "Baby Park" para Días de Semana
        $products[] = $this->createProductData(
            'Brazalete Baby Park (Martes a Viernes)',
            'Todas las Horas',
            $precio_brazalete_semana,
            'Pass Baby Park',
            true
        );

        // Brazalete "Baby Park" para Fines de Semana
        $products[] = $this->createProductData(
            'Brazalete Baby Park (Sabado a Domingo)',
            'Todas las Horas',
            $precio_brazalete_fin_semana,
            'Pass Baby Park',
            true
        );

        // Generar calcetines por talla con el nuevo precio
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
     * Añadimos un parámetro para el sufijo del nombre.
     */
    private function generateBrazaletesByHour(float $price, string $nameSuffix = ''): array
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
                "Brazalete $color" . $nameSuffix, // Agregamos el sufijo al nombre
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
                "Media Talla $talla",
                "Calcetines cómodos y suaves, talla $talla.",
                $price,
                'Medias'
            );
        }

        return $calcetines;
    }
}
