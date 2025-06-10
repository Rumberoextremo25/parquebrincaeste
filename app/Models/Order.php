<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'order_date',
        'total_amount',
        'status',
        'billing_full_name',
        'billing_email',
        'billing_phone',
        'billing_address',
        'billing_city',
        'billing_postal_code',
        'payment_method',
        'payment_bank_name',
        'payment_phone_number',
        'payment_cedula',
        'payment_dynamic_key',
        'promo_code',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'order_date' => 'datetime',
        'total_amount' => 'decimal:2', // Asegura que se caste a decimal con 2 dígitos
    ];

    /**
     * Get the user that owns the order.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the order items for the order.
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}