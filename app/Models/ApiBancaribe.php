<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiBancaribe extends Model
{
    use HasFactory;

    protected $table = 'api_bancaribe';
    protected $fillable = [
        'token',
        'refresh_token',
        'default_token'
    ];
}