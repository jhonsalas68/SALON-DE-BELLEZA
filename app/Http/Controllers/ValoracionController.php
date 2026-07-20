<?php

namespace App\Http\Controllers;

use App\Models\Valoracion;
use App\Models\Cita;
use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\LogsActivity;

class ValoracionController extends Controller
{
    use LogsActivity;

    public function index()
    {
        $valoraciones = Valoracion::with(['cliente', 'estilista', 'cita.servicio'])
            ->orderBy('fecha', 'desc')
            ->paginate(15);

        // Promedio global y por estilista
        $promedioGlobal = Valoracion::avg('estrellas') ?: 0;
        
        $rendimientoEstilistas = User::whereHas('role', function($q) {
                $q->where('slug', 'estilista');
            })
            ->withAvg('valoracionesRecibidas as promedio_estrellas', 'estrellas')
            ->withCount('valoracionesRecibidas as total_opiniones')
            ->get();

        return view('valoraciones.index', compact('valoraciones', 'promedioGlobal', 'rendimientoEstilistas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cita_id' => 'required|exists:citas,id',
            'estrellas' => 'required|integer|min:1|max:5',
            'comentario' => 'nullable|string|max:1000',
        ]);

        $cita = Cita::findOrFail($request->cita_id);

        if ($cita->estado !== 'completada') {
            return back()->with('error', 'Solo puedes calificar servicios completados.');
        }

        // Verificar si ya fue valorada
        $existe = Valoracion::where('cita_id', $cita->id)->exists();
        if ($existe) {
            return back()->with('error', 'Esta cita ya ha sido valorada previamente.');
        }

        $valoracion = Valoracion::create([
            'cita_id' => $cita->id,
            'cliente_id' => $cita->cliente_id,
            'estilista_id' => $cita->estilista_id,
            'estrellas' => $request->estrellas,
            'comentario' => $request->comentario,
            'fecha' => now(),
        ]);

        if (auth()->check()) {
            $this->logActivity('CREATE_REVIEW', "Valoración de {$request->estrellas} estrellas registrada para Cita ID: {$cita->id}", $valoracion->toArray());
        }

        return back()->with('success', '¡Muchas gracias por valorar nuestro servicio!');
    }
}
