// database/migrations/YYYY_MM_DD_HHMMSS_create_orders_table.php

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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Relaciona con la tabla 'users'
            $table->dateTime('order_date'); // Fecha y hora del pedido
            $table->decimal('total_amount', 10, 2); // Monto total del pedido (10 dígitos en total, 2 decimales)
            $table->string('status')->default('pending'); // Estado del pedido (pending, completed, cancelled, etc.)

            // Campos para la información de facturación/envío
            $table->string('billing_full_name');
            $table->string('billing_email')->nullable();
            $table->string('billing_phone')->nullable();
            $table->string('billing_address');
            $table->string('billing_city');
            $table->string('billing_postal_code')->nullable();

            // Campos para el método de pago (si es necesario almacenarlos aquí)
            $table->string('payment_method')->nullable(); // 'mobile-payment', 'in-store', 'credit_card', etc.
            $table->string('payment_bank_name')->nullable(); // Solo para pago móvil
            $table->string('payment_phone_number')->nullable(); // Solo para pago móvil
            $table->string('payment_cedula')->nullable(); // Solo para pago móvil
            $table->string('payment_dynamic_key')->nullable(); // Solo para pago móvil

            $table->string('promo_code')->nullable(); // Código de promoción aplicado

            $table->timestamps(); // created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
