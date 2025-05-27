<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    // Definir la tabla si el nombre no es plural de forma automática
    protected $table = 'products';

    // Definir los atributos que se pueden asignar masivamente
    protected $fillable = [
        'name',
        'description',
        'price',
        'stock',
        'size',
        'time',
        'image_url', // Si tienes una columna para la imagen
    ];

    // Si necesitas definir relaciones, puedes hacerlo aquí
    public function orders()
    {
        return $this->hasMany(Order::class); // Relación con el modelo Order
    }

    // Otros métodos y funciones del modelo pueden ir aquí
}

