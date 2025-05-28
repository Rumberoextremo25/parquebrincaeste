<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Factura extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre_completo',
        'correo',
        'telefono',
        'direccion',
        'ciudad',
        'codigo_postal',
        'promoCode',
        'paymentMethod',
        'monto',
    ];

    public function ventas()
    {
        return $this->hasMany(Venta::class);
    }
}
