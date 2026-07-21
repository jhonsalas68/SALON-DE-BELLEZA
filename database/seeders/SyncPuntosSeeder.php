<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Venta;
use App\Models\Cita;
use App\Models\User;
use App\Models\PuntosHistorial;

class SyncPuntosSeeder extends Seeder
{
    public function run(): void
    {
        $ventas = Venta::where('estado_pago', 'completado')->whereNotNull('cliente_id')->get();
        foreach ($ventas as $v) {
            $puntos = (int) floor($v->total / 10);
            if ($puntos > 0) {
                $c = User::find($v->cliente_id);
                if ($c) {
                    $existe = PuntosHistorial::where('venta_id', $v->id)->exists();
                    if (!$existe) {
                        $c->increment('puntos', $puntos);
                        PuntosHistorial::create([
                            'user_id' => $c->id,
                            'puntos' => $puntos,
                            'tipo' => 'ganado',
                            'descripcion' => "Ganado por compra realizada (Venta ID: {$v->id})",
                            'venta_id' => $v->id
                        ]);
                    }
                }
            }
        }

        $citas = Cita::where('estado', 'completada')->whereNotNull('cliente_id')->with('servicio')->get();
        foreach ($citas as $ci) {
            $precio = $ci->servicio ? $ci->servicio->precio : 0;
            $puntos = (int) floor($precio / 10);
            if ($puntos > 0) {
                $c = User::find($ci->cliente_id);
                if ($c) {
                    $existe = PuntosHistorial::where('cita_id', $ci->id)->exists();
                    if (!$existe) {
                        $c->increment('puntos', $puntos);
                        PuntosHistorial::create([
                            'user_id' => $c->id,
                            'puntos' => $puntos,
                            'tipo' => 'ganado',
                            'descripcion' => "Ganado por servicio completado (Cita ID: {$ci->id})",
                            'cita_id' => $ci->id
                        ]);
                    }
                }
            }
        }
    }
}
