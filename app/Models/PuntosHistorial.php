<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PuntosHistorial extends Model
{
    protected $table = 'puntos_historial';

    protected $fillable = [
        'user_id',
        'puntos',
        'tipo',
        'descripcion',
        'venta_id',
        'cita_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }

    public function cita()
    {
        return $this->belongsTo(Cita::class);
    }
}
