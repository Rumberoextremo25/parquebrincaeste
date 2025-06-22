<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tickets'; // Ensure this matches your table name

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'order_number',
        'customer_name',
        'customer_email',
        'customer_phone',
        'shipping_address',
        'city',
        'postal_code',
        'promo_code',
        'payment_method',
        'total_amount',
        'status',
        'bank_name',          // For mobile payment
        'sender_phone',       // For mobile payment
        'sender_id_number',   // For mobile payment
        'reference_number',   // For mobile payment
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'total_amount' => 'decimal:2', // Cast to decimal with 2 places
    ];

    /**
     * Get the user that owns the ticket.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the ticket items for the ticket.
     */
    public function ticketItems()
    {
        return $this->hasMany(TicketItem::class);
    }

    /**
     * Get the sales entries for this ticket (if you've added ticket_id to ventas).
     * This is conditional on your 'ventas' table schema.
     */
    public function ventas()
    {
        // Assuming you have a 'ticket_id' foreign key in your 'ventas' table
        return $this->hasMany(Venta::class, 'ticket_id');
    }
}
