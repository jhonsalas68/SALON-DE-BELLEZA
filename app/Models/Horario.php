<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Horario extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'dia_semana',
        'hora_inicio',
        'hora_fin',
        'activo',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
