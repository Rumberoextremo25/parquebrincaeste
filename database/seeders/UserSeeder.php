<?php  

namespace Database\Seeders;  

use Illuminate\Database\Seeder;  
use Spatie\Permission\Models\Permission;  
use Spatie\Permission\Models\Role;  

class UserSeeder extends Seeder  
{  
    /**  
     * Run the database seeds.  
     *  
     * @return void  
     */  
    public function run()  
    {  
        // Crear roles si no existen  
        if (!Role::where('name', 'super-admin')->exists()) {  
            Role::create(['name' => 'super-admin']);  
        }  

        if (!Role::where('name', 'user')->exists()) {  
            Role::create(['name' => 'user']);  
        }  

        // Crear permisos si no existen  
        if (!Permission::where('name', 'dashboard')->exists()) {  
            $permission = Permission::create(['name' => 'dashboard']);  
            $permission->syncRoles(['super-admin', 'user']);  
        }  
    }  
}