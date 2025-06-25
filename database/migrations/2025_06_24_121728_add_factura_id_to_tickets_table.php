// database/migrations/XXXX_XX_XX_XXXXXX_add_factura_id_to_tickets_table.php

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
        Schema::table('tickets', function (Blueprint $table) {
            // Añade la columna factura_id.
            // Es importante que sea unsignedBigInteger y nullable
            // si no es estrictamente requerido en todos los casos
            // o si la factura se puede crear después.
            $table->foreignId('factura_id')
                  ->nullable() // Permite que sea NULL si no hay factura asociada inicialmente
                  ->after('promo_code') // O después de cualquier columna existente donde quieras ubicarla
                  ->constrained('facturas') // Crea una clave foránea a la tabla 'facturas'
                  ->onDelete('set null'); // Si una factura es eliminada, factura_id en tickets se pone a NULL
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            // Es crucial eliminar la clave foránea antes de eliminar la columna
            $table->dropConstrainedForeignId('factura_id');
            $table->dropColumn('factura_id');
        });
    }
};
