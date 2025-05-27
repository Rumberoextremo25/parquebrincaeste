<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Finanza extends Model
{
    // Define las propiedades y métodos necesarios
    protected $fillable = ['ingreso', 'gasto', 'fecha']; // Ajusta según tus campos
}
