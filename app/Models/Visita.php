<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visita extends Model
{
    // Definir la tabla si no sigue la convención pluralizada
    protected $table = 'visitas';

    // Campos que se pueden asignar masivamente
    protected $fillable = ['ip'];

    // Si no usas timestamps, puedes agregar:
    // public $timestamps = false;
}
