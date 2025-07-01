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
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade'); // Hice nullable user_id por si no es obligatorio
            $table->string('order_number')->unique();
            $table->string('nombre_completo')->nullable();
            $table->string('correo')->nullable();
            $table->string('telefono')->nullable();
            $table->string('direccion')->nullable();
            $table->string('ciudad')->nullable();
            $table->string('codigo_postal')->nullable();
            $table->string('promo_code')->nullable();
            $table->string('payment_method');
            // MODIFICACIÓN: Ajuste del default status para reflejar los nuevos métodos de pago
            $table->string('status')->default('pending_payment'); // Un estado más genérico o 'pending_payment_card'

            // Campos específicos de pago móvil
            $table->string('banco_remitente')->nullable();
            $table->string('numero_telefono_remitente')->nullable();
            $table->string('cedula_remitente')->nullable();

            // MODIFICACIÓN: Nuevos campos para tarjeta de crédito/débito
            // Nota de seguridad: En un entorno real, estos campos sensibles NO se deberían almacenar directamente
            // en la base de datos a menos que se use tokenización y se cumplan estrictas normas PCI DSS.
            // Para este ejercicio, los incluimos, pero ten en cuenta las implicaciones de seguridad.
            $table->string('card_number')->nullable(); // Número de tarjeta (considerar enmascarar o tokenizar)
            $table->string('card_holder_name')->nullable(); // Nombre del tarjetahabiente
            $table->string('card_expiry_month', 2)->nullable(); // Mes de vencimiento (ej: '01' a '12')
            $table->string('card_expiry_year', 2)->nullable(); // Año de vencimiento (ej: '25' para 2025)
            $table->string('card_cvv', 4)->nullable(); // CVV/CVC (3 o 4 dígitos)

            // Campo de referencia de pago (para pago móvil o terminal POS de tarjeta)
            $table->string('numero_referencia_pago')->nullable();

            $table->decimal('monto_total', 10, 2);
            $table->timestamps();

            // MODIFICACIÓN: Añadir factura_id como foreign key
            // Asumiendo que la factura se crea primero o junto con el ticket
            $table->foreignId('factura_id')->nullable()->constrained('facturas')->onDelete('set null');
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
