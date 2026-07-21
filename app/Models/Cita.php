<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cita extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente_id',
        'estilista_id',
        'servicio_id',
        'fecha',
        'hora',
        'estado',
        'notas'
    ];

    public function cliente()
    {
        return $this->belongsTo(User::class, 'cliente_id');
    }

    public function estilista()
    {
        return $this->belongsTo(User::class, 'estilista_id');
    }

    public function servicio()
    {
        return $this->belongsTo(Servicio::class, 'servicio_id');
    }

    public function valoracion()
    {
        return $this->hasOne(Valoracion::class, 'cita_id');
    }
}
