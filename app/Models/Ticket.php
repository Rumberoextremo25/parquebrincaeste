<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    // Define las propiedades y métodos necesarios
    protected $fillable = ['id', 'product', 'quantity', 'price']; // Ajusta según tus campos
}
