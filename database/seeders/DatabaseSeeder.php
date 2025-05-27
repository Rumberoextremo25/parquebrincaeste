<?php

namespace Database\Seeders;

use GuzzleHttp\Client;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Database\Seeders\ClientesTableSeeder;

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
            ProductsTableSeeder::class,
            FinanzasSeeder::class,
            VentasSeeder::class,
            ClientesTableSeeder::class,

        ]);
        Schema::enableForeignKeyConstraints();
    }
}
