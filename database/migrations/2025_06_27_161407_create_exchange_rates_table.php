<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('exchange_rates', function (Blueprint $table) {
            $table->id();
            $table->decimal('rate', 10, 4); // Store rate with 4 decimal places for precision
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // Who changed it
            $table->string('source')->nullable(); // e.g., 'BCV', 'Manual'
            $table->timestamps(); // created_at will be the date of the change
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exchange_rates');
    }
};
