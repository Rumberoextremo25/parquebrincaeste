<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExchangeRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'rate',
        'user_id',
        'source',
    ];

    /**
     * Get the user that updated the exchange rate.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the current exchange rate.
     * This is a simple way to get the latest rate.
     */
    public static function current()
    {
        return static::latest()->first();
    }
}
