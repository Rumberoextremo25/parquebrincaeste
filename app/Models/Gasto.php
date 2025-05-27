<?php  

namespace App\Models;  

use Illuminate\Database\Eloquent\Factories\HasFactory;  
use Illuminate\Database\Eloquent\Model;  

class Gasto extends Model  
{  
    use HasFactory;  

    // Especificar la tabla si es diferente del plural del nombre del modelo  
    protected $table = 'gastos';  

    // Los atributos que pueden ser asignados de manera masiva  
    protected $fillable = [  
        'descripcion',  
        'monto',  
        'fecha',  
        'categoria', // Puedes agregar una categoría si es relevante  
    ];  

    // Si tienes una relación con la tabla de usuarios  
    public function user()  
    {  
        return $this->belongsTo(User::class); // Asumiendo que cada gasto pertenece a un usuario  
    }  
}