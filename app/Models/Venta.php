<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'factura_id',
        'product_id',
        'quantity',
        'price',
        'subtotal',
        'user_id',
        'ticket_id',
        'order_number',
        'fecha',
    ];

    /**
     * Get the factura that owns the Venta.
     */
    public function factura()
    {
        return $this->belongsTo(Factura::class);
    }

    /**
     * Get the product that was sold.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the user who made the sale.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the ticket that owns the Venta item.
     */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
}