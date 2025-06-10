// database/migrations/YYYY_MM_DD_HHMMSS_create_order_items_table.php

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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade'); // Relaciona con la tabla 'orders'
            $table->foreignId('product_id')->constrained()->onDelete('cascade'); // Relaciona con la tabla 'products'

            $table->integer('quantity'); // Cantidad de este producto en el pedido
            $table->decimal('price', 10, 2); // Precio unitario del producto en el momento de la compra
            $table->decimal('subtotal', 10, 2); // Subtotal de este ítem (quantity * price)

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
