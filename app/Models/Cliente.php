<?php  

namespace App\Models;  

use Illuminate\Database\Eloquent\Factories\HasFactory;  
use Illuminate\Database\Eloquent\Model;  

class Cliente extends Model  
{  
    use HasFactory;  

    // Definimos la tabla si no sigue la convención  
    protected $table = 'clientes'; 

    // Campos que pueden ser asignados masivamente  
    protected $fillable = [  
        'nombre',  
        'apellido',  
        'email',  
        'telefono',  
        'direccion',  
    ];  

    // Si necesitas definir fechas, puedes usar la propiedad 'dates'  
    protected $dates = [  
        'created_at',  
        'updated_at',  
        // Agrega otras fechas relevantes aquí  
    ];  
}
