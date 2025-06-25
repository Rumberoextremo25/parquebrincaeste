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
            $table->string('status')->default('pending_payment_cash');
            $table->string('banco_remitente')->nullable();
            $table->string('numero_telefono_remitente')->nullable();
            $table->string('cedula_remitente')->nullable();
            $table->string('numero_referencia_pago')->nullable();
            $table->decimal('monto_total', 10, 2);
            $table->timestamps();

            // No añades factura_id ni numero_factura aquí.
            // La relación es que Factura tiene un ticket_id.
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
