<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('visitas', function (Blueprint $table) {
        $table->id();
        $table->string('ip')->index(); // La IP del visitante
        $table->unsignedBigInteger('user_id')->nullable()->index(); // ID del usuario si está logueado
        $table->text('user_agent')->nullable(); // Información del navegador y SO
        $table->string('url_visitada', 2048); // La URL que visitó (usar TEXT si es muy larga)
        $table->string('referrer', 2048)->nullable(); // URL de origen (de dónde vino)
        $table->string('session_id')->nullable()->index(); // ID de la sesión de Laravel
        $table->string('pais')->nullable(); // País inferido de la IP
        // $table->string('ciudad')->nullable(); // Ciudad inferida de la IP (opcional, más granular)
        $table->timestamp('created_at')->useCurrent(); // Marca de tiempo de la visita

        // Opcional: Clave foránea si tienes una tabla de usuarios
        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visitas');
    }
};
