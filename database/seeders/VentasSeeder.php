<?php  

namespace Database\Seeders;  

use Illuminate\Database\Seeder;  
use Illuminate\Support\Facades\DB; 
use App\Models\Product; // Importa el modelo Producto

class VentasSeeder extends Seeder
{
    public function run()
    {
        // Obtener todos los productos y facturas existentes
        $productos = Product::all();
        $facturas = \App\Models\Factura::all();

        if ($productos->isEmpty() || $facturas->isEmpty()) {
            $this->command->warn('Asegúrate de que hay productos y facturas en la base de datos antes de correr este seeder.');
            return;
        }

        $ventas = [];

        for ($i = 0; $i < 20; $i++) { // genera 20 ventas
            $cantidad = rand(1, 10);
            $producto = $productos->random();
            $factura = $facturas->random();

            // Valor por defecto en caso de null
            $monto = $producto->precio ?? 10;
            $total = $cantidad * $monto;

            $ventas[] = [
                'factura_id' => $factura->id,
                'producto_id' => $producto->id,
                'cantidad' => $cantidad,
                'monto' => $total, // Aquí asignamos el monto total
                'fecha' => now()->format('Y-m-d'), // Fecha actual en formato Y-m-d
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('ventas')->insert($ventas);
    }
}