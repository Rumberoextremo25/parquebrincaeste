<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductsSeeder extends Seeder
{
    public function run()
    {
        // Precios definidos para brazaletes
        // Estas constantes ahora solo reflejan los precios base que el frontend ajustará.
        $precio_brazalete_general = 5.00; // Precio base para brazaletes de trampolines
        $precio_calcetines = 1.50; // Costo actualizado para las medias
        $precio_baby_park = 6.00; // Costo fijo para Baby Park todos los días

        $products = [];

        // MODIFICACIÓN: Generar UN SOLO conjunto de brazaletes por hora para trampolines.
        // El frontend determinará el precio ($5 o $6) según el día seleccionado.
        $products = array_merge($products, $this->generateBrazaletesByHour(
            $precio_brazalete_general // Usamos un precio base, el frontend lo ajusta
        ));

        // MODIFICACIÓN: Brazalete "Baby Park" con precio fijo de $6 y nombre sin sufijo de día
        // Solo se crea un único producto "Brazalete Baby Park"
        $products[] = $this->createProductData(
            'Brazalete Baby Park', // Nombre sin sufijo de día
            'Acceso a todas las horas del área Baby Park.', // Descripción más general
            $precio_baby_park, // Usar el precio fijo
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
        bool   $onlyChildren = false,
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
     * Ya no se necesita el nameSuffix, ya que el precio se ajusta en el frontend.
     */
    private function generateBrazaletesByHour(float $price): array
    {
        $brazaletes = [];
        $horas = [
            '11:00 AM a 12:00 PM' => 'Azul',
            '12:00 PM a 1:00 PM' => 'Amarillo',
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
                "Brazalete $color", // Nombre sin sufijo de día
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
