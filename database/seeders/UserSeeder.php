<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**  
     * Run the database seeds.  
     *  
     * @return void  
     */
    public function run()
    {
        /**
         * Creación de roles
         */
        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin']);
        $userRole = Role::firstOrCreate(['name' => 'user']);
        $userRole = Role::firstOrCreate(['name' => 'caja']);

        /**
         * Creación de permisos
         */
        $dashboardPermission = Permission::firstOrCreate(['name' => 'dashboard']);
        $dashboardPermission->syncRoles([$superAdminRole, $userRole]);

        /**
         * Creación de usuario admin
         */
        $superAdminUser = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Super Admin',
                'active' => 1,
                'phone' => '1234567890',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
            ]
        );
        $superAdminUser->assignRole($superAdminRole);

        /**
         * Creación de usuario generico
         */
        $genericUser = User::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'user generic',
                'active' => 1,
                'phone' => '1234567890',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
            ]
        );
        $genericUser->assignRole($userRole);

        /**
         * Creación de usuario generico
         */
        $genericUser = User::firstOrCreate(
            ['email' => 'cajasbrincaeste@gmail.com'],
            [
                'name' => 'Cajeros',
                'active' => 1,
                'phone' => 'Brincaeste2024',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
            ]
        );
        $genericUser->assignRole($userRole);
    }
}
