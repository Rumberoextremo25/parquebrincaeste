<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'amount',
        'date',
        'customer_name',
        // Agrega más campos según sea necesario
    ];
}
