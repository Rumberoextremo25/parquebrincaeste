// database/migrations/XXXX_XX_XX_XXXXXX_create_ventas_table.php

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
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();

            // Clave foránea para la factura (nullable para flexibilidad)
            $table->foreignId('factura_id')->nullable()->constrained('facturas')->onDelete('set null');

            // Clave foránea para el producto
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade'); // Cambiado a product_id para consistencia

            $table->integer('quantity'); // Cantidad de este producto en la venta
            $table->decimal('price', 10, 2); // Precio unitario del producto en el momento de la venta
            $table->decimal('subtotal', 10, 2); // Subtotal de esta línea de venta (cantidad * precio)

            // Clave foránea para el usuario que realizó la compra (nullable para usuarios no registrados)
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');

            // Clave foránea para el ticket asociado (nullable si la venta puede existir sin un ticket directo)
            $table->foreignId('ticket_id')->nullable()->constrained('tickets')->onDelete('set null');

            // Número de orden del ticket asociado (nullable para flexibilidad, pero útil para referencia)
            $table->string('order_number')->nullable();

            $table->date('fecha')->nullable(); // Campo de fecha de la venta (nullable, si se puede inferir de created_at)

            $table->timestamps(); // created_at y updated_at
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
