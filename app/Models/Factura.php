<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Factura extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'user_id',
        'numero_factura',
        'monto_total',
        'fecha_emision',
        'status',
        // --- ¡AÑADE ESTOS AL FILLABLE! ---
        'nombre_completo',
        'correo',
        'telefono',
        'direccion',
        'ciudad',
        'codigo_postal',
        'banco_remitente', // Para pago móvil
        'numero_telefono_remitente', // Para pago móvil
        'cedula_remitente', // Para pago móvil
        'numero_referencia_pago', // Para pago móvil
    ];

    // ... (Tus relaciones si las tienes) ...
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
