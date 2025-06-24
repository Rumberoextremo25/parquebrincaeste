<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $table = 'tickets';

    protected $fillable = [
        'user_id',
        'order_number',
        'nombre_completo', // ¡Añade este y los demás!
        'correo',
        'telefono',
        'direccion',
        'ciudad',
        'codigo_postal',
        'promo_code',
        'payment_method',
        'monto_total',
        'status',
        'banco_remitente',
        'numero_telefono_remitente',
        'cedula_remitente',
        'numero_referencia_pago',
        'factura_id',
    ];

    protected $casts = [
        'monto_total' => 'decimal:2', // Correcto, coincide con 'monto_total'
    ];

    /**
     * Obtiene el usuario propietario del ticket (orden).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtiene los ítems del ticket (ítems de línea) para este ticket (orden).
     */
    public function ticketItems()
    {
        return $this->hasMany(TicketItem::class);
    }

    /**
     * Obtiene las entradas de ventas para este ticket (si has añadido ticket_id a ventas).
     * Esto es condicional al esquema de tu tabla 'ventas'.
     */
    public function ventas()
    {
        // Asumiendo que tienes una clave foránea 'ticket_id' en tu tabla 'ventas'
        return $this->hasMany(Venta::class, 'ticket_id');
    }

    // --- Nuevo Accessor ---
    // Esto calculará la cantidad total de productos en todos los ticket_items para este ticket (orden).
    protected $appends = ['total_productos_cantidad'];

    public function getTotalProductosCantidadAttribute()
    {
        return $this->ticketItems->sum('quantity');
    }

    public function factura() // <-- ¡AÑADE ESTE MÉTODO!
    {
        return $this->belongsTo(Factura::class, 'factura_id', 'id');
    }
}