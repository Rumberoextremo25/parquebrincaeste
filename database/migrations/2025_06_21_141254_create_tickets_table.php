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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('order_number')->unique();
            $table->string('nombre_completo'); // <- Asegúrate de que esto esté aquí
            $table->string('correo'); // <- Si lo guardas en tickets
            $table->string('telefono'); // <-
            $table->string('direccion'); // <-
            $table->string('ciudad'); // <-
            $table->string('codigo_postal'); // <-
            $table->string('promo_code')->nullable(); // Puede ser null
            $table->string('payment_method');
            $table->string('status')->default('pending_payment_cash'); // O el default que consideres
            $table->string('banco_remitente')->nullable(); // Nullable si solo aplica para pago móvil
            $table->string('numero_telefono_remitente')->nullable();
            $table->string('cedula_remitente')->nullable();
            $table->string('numero_referencia_pago')->nullable();
            $table->decimal('monto_total', 10, 2); // DECIMAL para montos monetarios
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
