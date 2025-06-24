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
        Schema::create('ticket_items', function (Blueprint $table) {
            $table->id();
            // Clave foránea que enlaza cada ítem a un ticket.
            // Cuando un ticket se elimina, sus ítems también se eliminan.
            $table->foreignId('ticket_id')->constrained('tickets')->onDelete('cascade');
            
            // Clave foránea que enlaza cada ítem a un producto de tu tabla 'products'.
            // Asegúrate de que tienes una tabla `products` o ajusta el nombre.
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');

            $table->integer('quantity'); // Cantidad de este producto en el ticket
            $table->decimal('price', 10, 2); // Precio unitario del producto en el momento de la compra
            $table->decimal('subtotal', 10, 2);

            $table->timestamps(); // created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_items');
    }
};
