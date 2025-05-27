<?php  

namespace App\Models;  

use Illuminate\Database\Eloquent\Factories\HasFactory;  
use Illuminate\Database\Eloquent\Model;  

class Venta extends Model  
{  
    use HasFactory;  

    // Definimos la tabla si no sigue la convención  
    protected $table = 'ventas'; // Nombre de la tabla en la base de datos  

    // Campos que pueden ser asignados masivamente  
    protected $fillable = [  
        'cliente_id',  
        'producto_id',  
        'cantidad',  
        'precio_unitario',  
        'total',  
        'fecha_venta',
        'fecha',  
    ];  

    // Si necesitas definir fechas, puedes usar la propiedad 'dates'  
    protected $dates = [  
        'fecha_venta',  
        'created_at',  
        'updated_at',  
    ];  

    // Relaciones (Ejemplo)  
    public function cliente()  
    {  
        return $this->belongsTo(Cliente::class);  
    }  

    public function producto()  
    {  
        return $this->belongsTo(Product::class);  
    }  
}
