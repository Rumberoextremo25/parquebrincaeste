<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscriber extends Model
{
    use HasFactory;

    // Definir la tabla asociada si el nombre no sigue la convención
    protected $table = 'subscribers';

    // Especificar los atributos que son asignables en masa
    protected $fillable = [
        'email',
    ];

    // Opcional: si deseas agregar validaciones o métodos adicionales, puedes hacerlo aquí
}
