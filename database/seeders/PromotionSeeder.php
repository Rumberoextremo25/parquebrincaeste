<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Promotion;
use App\Models\Ticket;
use App\Enums\PromotionStatus;

class PromotionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Truncar las tablas
        Promotion::truncate();
        DB::table('promotions')->truncate();

        // Crear promociones
        $promotions = Promotion::insert([
            ['name' => 'brinceste', 'quantity' => 10, 'status' => PromotionStatus::ACTIVE],
            ['name' => 'brincamania', 'quantity' => 15, 'status' => PromotionStatus::ACTIVE],
            ['name' => 'brincabulguer', 'quantity' => 20, 'status' => PromotionStatus::ACTIVE],
        ]);

        // Obtener todos los tickets
        $tickets = Ticket::all();

        // Asociar promociones con tickets
        foreach ($tickets as $ticket) {
            $pivot_promotions = [];
            foreach ($promotions as $promotion) {
                $pivot_promotions[$promotion->id] = [
                    'remaining' => $promotion->quantity,
                    'quantity' => $promotion->quantity,
                ];
            }

            $ticket->promotions()->attach($pivot_promotions);
        }
    }
}

