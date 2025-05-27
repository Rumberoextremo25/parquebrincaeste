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
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained()->onDelete('cascade'); // Relación con la tabla clientes  
            $table->foreignId('producto_id')->constrained()->onDelete('cascade'); // Relación con la tabla productos  
            $table->integer('cantidad');
            $table->decimal('precio_unitario', 10, 2);
            $table->decimal('monto', 10, 2); // Asegúrate de que esta línea esté presente  
            $table->decimal('total', 10, 2);
            $table->date('fecha_venta'); // Fecha de la venta  
            $table->date('fecha'); // Nuevo campo fecha  
            $table->timestamps(); // Campos created_at y updated_at  
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};
