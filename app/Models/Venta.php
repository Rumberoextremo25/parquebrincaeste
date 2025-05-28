<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;

class Venta extends Model
{
    use HasFactory;

    protected $fillable = [
        'factura_id',
        'producto_id',
        'cantidad',
        'precio',
    ];

    public function factura()
    {
        return $this->belongsTo(Factura::class);
    }

    public function producto()
    {
        return $this->belongsTo(Product::class); // Asegúrate de tener el modelo Producto
    }
}