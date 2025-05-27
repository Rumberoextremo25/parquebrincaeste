<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('finanzas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venta_id')->nullable()->constrained('ventas')->onDelete('cascade'); // Clave foránea a la tabla ventas
            $table->decimal('ingreso', 10, 2)->nullable(); // Monto de ingreso
            $table->decimal('gasto', 10, 2)->nullable(); // Monto de gasto
            $table->date('fecha'); // Fecha de la finanza
            $table->timestamps(); // Campos created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('finanzas');
    }
};
