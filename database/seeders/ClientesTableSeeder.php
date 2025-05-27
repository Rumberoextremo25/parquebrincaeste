<?php  

namespace Database\Seeders;  

use Illuminate\Database\Seeder;  
use App\Models\Cliente; // Asegúrate de importar tu modelo Cliente  

class ClientesTableSeeder extends Seeder  
{  
    public function run()  
    {  
        // Crear datos de ejemplo para la tabla clientes  
        $clientes = [  
            [  
                'nombre' => 'Juan',  
                'apellido' => 'Pérez',  
                'email' => 'juan.perez@example.com',  
                'telefono' => '1234567890',  
                'direccion' => 'Calle Falsa 123'  
            ],  
            [  
                'nombre' => 'María',  
                'apellido' => 'Gómez',  
                'email' => 'maria.gomez@example.com',  
                'telefono' => '0987654321',  
                'direccion' => 'Avenida Siempre Viva 742'  
            ],  
            [  
                'nombre' => 'Carlos',  
                'apellido' => 'Sánchez',  
                'email' => 'carlos.sanchez@example.com',  
                'telefono' => '2345678901',  
                'direccion' => 'Boulevard de los Sueños 456'  
            ],  
            [  
                'nombre' => 'Laura',  
                'apellido' => 'Hernández',  
                'email' => 'laura.hernandez@example.com',  
                'telefono' => '3456789012',  
                'direccion' => 'Calle del Cielo 789'  
            ],  
            [  
                'nombre' => 'Pedro',  
                'apellido' => 'Martínez',  
                'email' => 'pedro.martinez@example.com',  
                'telefono' => '4567890123',  
                'direccion' => 'Avenida del Sol 101'  
            ],  
            [  
                'nombre' => 'Ana',  
                'apellido' => 'López',  
                'email' => 'ana.lopez@example.com',  
                'telefono' => '5678901234',  
                'direccion' => 'Calle 111'  
            ],  
            [  
                'nombre' => 'Luis',  
                'apellido' => 'Pérez',  
                'email' => 'luis.perez@example.com',  
                'telefono' => '6789012345',  
                'direccion' => 'Avenida 222'  
            ],  
            [  
                'nombre' => 'Patricia',  
                'apellido' => 'Díaz',  
                'email' => 'patricia.diaz@example.com',  
                'telefono' => '7890123456',  
                'direccion' => 'Calle 333'  
            ],  
            [  
                'nombre' => 'Fernando',  
                'apellido' => 'Jiménez',  
                'email' => 'fernando.jimenez@example.com',  
                'telefono' => '8901234567',  
                'direccion' => 'Avenida 444'  
            ],  
            [  
                'nombre' => 'Carmen',  
                'apellido' => 'Torres',  
                'email' => 'carmen.torres@example.com',  
                'telefono' => '9012345678',  
                'direccion' => 'Avenida 555'  
            ],  
        ];  

        // Insertar los clientes en la base de datos  
        foreach ($clientes as $cliente) {  
            Cliente::create($cliente);  
        }  
    }  
}
