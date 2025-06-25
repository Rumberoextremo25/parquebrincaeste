<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visita extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'visitas'; // Define el nombre de la tabla si no sigue la convención de plural en inglés (visits)

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'ip',
        'user_id',
        'user_agent',
        'url_visitada',
        'referrer',
        'session_id',
        'pais',
        // 'ciudad', // Si decides incluir este campo en tu migración
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false; // Desactiva los timestamps automáticos (updated_at)
                                // ya que solo tenemos 'created_at' y lo manejamos con useCurrent() en la migración

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime', // Asegura que 'created_at' se maneje como un objeto de fecha/hora
    ];

    // --- Relaciones (si aplica) ---

    /**
     * Get the user that owns the visit.
     */
    public function user()
    {
        return $this->belongsTo(User::class); // Asume que tienes un modelo User.php
    }
}
