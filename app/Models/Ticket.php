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
        // 'order_number', // Asegúrate de que esto esté en tu migración si lo usas
        'nombre_completo',
        'correo',
        'telefono', // Cambiado de 'customer_phone' a 'telefono' según tu migración
        'direccion', // Cambiado de 'shipping_address' a 'direccion' según tu migración
        'ciudad', // Cambiado de 'city' a 'ciudad' según tu migración
        'codigo_postal', // Cambiado de 'postal_code' a 'codigo_postal' según tu migración
        'promo_code',
        'payment_method',
        'monto_total', // Cambiado de 'total_amount' a 'monto_total' según tu migración
        'status',
        'banco_remitente', // Cambiado de 'bank_name' a 'banco_remitente' según tu migración
        'numero_telefono_remitente', // Cambiado de 'sender_phone' a 'numero_telefono_remitente' según tu migración
        'cedula_remitente', // Cambiado de 'sender_id_number' a 'cedula_remitente' según tu migración
        'numero_referencia_pago', // Cambiado de 'reference_number' a 'numero_referencia_pago' según tu migración
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
}