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
        Schema::create('facturas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('numero_factura')->unique();
            $table->decimal('monto_total', 10, 2);
            $table->date('fecha_emision');
            $table->date('fecha_uso_ticket')->nullable();
            $table->string('status')->default('pendiente');

            // --- ¡CAMPOS FALTANTES QUE CAUSAN EL ERROR! ---
            // Estos campos deben existir en tu tabla `facturas`
            $table->string('nombre_completo')->nullable(); // Si no es siempre requerido
            $table->string('correo')->nullable();         // Si no es siempre requerido
            $table->string('telefono')->nullable();       // Si no es siempre requerido
            $table->string('direccion')->nullable();      // Si no es siempre requerido
            $table->string('ciudad')->nullable();         // Si no es siempre requerido
            $table->string('codigo_postal')->nullable();  // Si no es siempre requerido

            // Campos específicos de pago móvil, también nullable en la factura si no aplican a todas
            $table->string('banco_remitente')->nullable();
            $table->string('numero_telefono_remitente')->nullable();
            $table->string('cedula_remitente')->nullable();
            $table->string('numero_referencia_pago')->nullable();

            // MODIFICACIÓN: Nuevos campos para tarjeta de crédito/débito
            $table->string('card_number')->nullable(); // Número de tarjeta (puede ser enmascarado o tokenizado)
            $table->string('card_holder_name')->nullable(); // Nombre del tarjetahabiente
            $table->string('card_expiry_month', 2)->nullable(); // Mes de vencimiento (ej: '01' a '12')
            $table->string('card_expiry_year', 2)->nullable(); // Año de vencimiento (ej: '25' para 2025)
            $table->string('card_cvv', 4)->nullable(); // CVV/CVC (3 o 4 dígitos)

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facturas');
    }
};
