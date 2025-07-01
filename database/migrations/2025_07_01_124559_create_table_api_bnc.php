<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Ejecuta las migraciones (crea la tabla).
     */
    public function up(): void
    {
        Schema::create('api_bnc', function (Blueprint $table) {
            $table->id(); // Columna de clave primaria autoincremental

            // Token de acceso principal
            $table->text('token')->nullable()->comment('Token de acceso principal para la API de Bancaribe');

            // Token de refresco (para obtener nuevos tokens de acceso)
            $table->text('refresh_token')->nullable()->comment('Token de refresco para la API de Bancaribe');

            // Token por defecto (si la API usa un token estático o inicial sin autenticación)
            $table->text('default_token')->nullable()->comment('Token por defecto o inicial (si aplica)');

            // Fecha y hora de expiración del token de acceso
            $table->timestamp('expires_at')->nullable()->comment('Fecha y hora de expiración del token de acceso');

            $table->timestamps(); // Columnas created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * Revierte las migraciones (elimina la tabla).
     */
    public function down(): void
    {
        Schema::dropIfExists('api_bancaribes');
    }
};
