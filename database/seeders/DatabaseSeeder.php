<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Database\Seeders\ClientesTableSeeder;
use Database\Seeders\FacturasSeeder as SeedersFacturasSeeder;
use Database\Seeders\VentasSeeder as SeedersVentasSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        //dd(EventTypes::Event->value);
        Schema::disableForeignKeyConstraints();
        $this->call([
            UserSeeder::class,
            ProductsSeeder::class,
            FinanzasSeeder::class,
            SeedersFacturasSeeder::class,
            ClientesTableSeeder::class,
            SeedersVentasSeeder::class


        ]);
        Schema::enableForeignKeyConstraints();
    }
}
