<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderTicket extends Model
{
    use HasFactory;

    protected $table = 'order_ticket'; // Nombre de la tabla intermedia

    protected $fillable = [
        'order_id',
        'cart_item_id',
        'quantity',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function cartItem()
    {
        return $this->belongsTo(CartItem::class);
    }
}
