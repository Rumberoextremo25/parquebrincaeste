<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketItem extends Model
{
    use HasFactory;

    protected $table = 'ticket_items';

    protected $fillable = [
        'ticket_id',
        'product_id',
        'quantity',
        'price',
        // 'subtotal', // Asegúrate de que 'subtotal' esté realmente en tu migración si quieres usarlo
    ];

    protected $casts = [
        'price' => 'decimal:2',
        // 'subtotal' => 'decimal:2', // Conversión a decimal con 2 decimales
    ];

    /**
     * Obtiene el ticket (orden) al que pertenece este ítem de ticket.
     */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * Obtiene el producto asociado con el ítem del ticket.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}