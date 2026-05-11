<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
        'precio_compra',
        'precio_venta',
        'imagen',
        'stock',
        'fecha_caducidad',
        'promotor_id',
    ];

    public function promotor()
    {
        return $this->belongsTo(Promotor::class);
    }
}
