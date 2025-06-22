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
            // Si manejas usuarios registrados, puedes enlazar el ticket a un usuario.
            // Si el usuario no está autenticado al momento de la compra, user_id será nulo.
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');

            $table->string('nombre_completo');
            $table->string('correo');
            $table->string('telefono')->nullable(); // El teléfono es opcional en la compra, pero útil
            $table->string('direccion');
            $table->string('ciudad');
            $table->string('codigo_postal')->nullable(); // Código postal puede ser opcional dependiendo de tu logística

            $table->string('promo_code')->nullable(); // Para códigos de descuento

            $table->string('payment_method'); // 'in-store' o 'mobile-payment'
            $table->decimal('monto_total', 10, 2); // Monto total del ticket

            $table->string('status')->default('pending'); // Estado inicial: 'pending', 'completed', 'cancelled', etc.
            
            // Campos específicos para pago móvil
            $table->string('banco_remitente')->nullable();
            $table->string('numero_telefono_remitente')->nullable();
            $table->string('cedula_remitente')->nullable();
            $table->string('numero_referencia_pago')->nullable()->unique(); // La referencia debe ser única

            $table->timestamps(); // created_at y updated_at
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
