<?php  

namespace Database\Seeders;  

use Illuminate\Database\Seeder;  
use Illuminate\Support\Facades\DB;  
use App\Models\Cliente; // Importa el modelo Cliente  
use App\Models\Product; // Importa el modelo Producto  

class VentasSeeder extends Seeder  
{  
    public function run()  
    {  
        // Obtener todos los clientes y productos para relacionarlos  
        $clientes = Cliente::all();  
        $productos = Product::all();  

        if ($clientes->isEmpty() || $productos->isEmpty()) {  
            $this->command->warn('Asegúrate de que hay clientes y productos en la base de datos antes de ejecutar este seeder.');  
            return;  
        }  

        // Crear datos de ejemplo para la tabla ventas  
        $ventas = [];  
        foreach (range(1, 10) as $index) {  
            $cantidad = rand(1, 5); // Cantidad aleatoria entre 1 y 5  
            $precio_unitario = rand(10, 100); // Precio unitario aleatorio entre 10 y 100  
            $total = $cantidad * $precio_unitario; // Calculo del total  

            $ventas[] = [  
                'cliente_id' => $clientes->random()->id, // Escoge un cliente aleatorio  
                'producto_id' => $productos->random()->id, // Escoge un producto aleatorio  
                'cantidad' => $cantidad,  
                'precio_unitario' => $precio_unitario,  
                'monto' => $total, // Guardamos el monto directamente  
                'total' => $total, // También guardamos el total  
                'fecha_venta' => now(), // Fecha de la venta  
                'created_at' => now(),  
                'updated_at' => now(),  
            ];  
        }  

        // Insertar los datos en la tabla ventas  
        DB::table('ventas')->insert($ventas);  
    }  
}