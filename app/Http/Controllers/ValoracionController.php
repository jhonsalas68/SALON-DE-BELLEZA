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

        // Citas completadas elegibles para valorar
        $citasElegiblesQuery = Cita::with(['servicio', 'estilista', 'cliente', 'valoracion'])
            ->where('estado', 'completada');

        if ($user->hasRole('cliente')) {
            $citasElegiblesQuery->where('cliente_id', $user->id);
        }

        $citasPendientes = (clone $citasElegiblesQuery)->whereDoesntHave('valoracion')->get();
        $citasYaValoradas = (clone $citasElegiblesQuery)->whereHas('valoracion')->get();

        // Si no hay de cliente pero es admin/recepcionista, mostrar citas pendientes de valorar generales
        if ($citasPendientes->isEmpty() && !$user->hasRole('cliente')) {
            $citasPendientes = Cita::with(['servicio', 'estilista', 'cliente', 'valoracion'])
                ->where('estado', 'completada')
                ->whereDoesntHave('valoracion')
                ->orderBy('fecha', 'desc')
                ->limit(20)
                ->get();
        }

        return view('valoraciones.index', compact(
            'valoraciones', 
            'promedioGlobal', 
            'rendimientoEstilistas',
            'citasPendientes',
            'citasYaValoradas'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cita_id' => 'nullable|exists:citas,id',
            'estrellas' => 'required|integer|min:1|max:5',
            'comentario' => 'nullable|string|max:1000',
        ]);

        $citaId = null;
        $clienteId = auth()->id();
        $estilistaId = null;

        if ($request->filled('cita_id')) {
            $cita = Cita::findOrFail($request->cita_id);

            if ($cita->estado !== 'completada') {
                return back()->with('error', 'Solo puedes calificar servicios completados.');
            }

            // Verificar si ya fue valorada
            $existe = Valoracion::where('cita_id', $cita->id)->exists();
            if ($existe) {
                return back()->with('error', 'Esta cita ya ha sido valorada previamente.');
            }

            $citaId = $cita->id;
            $clienteId = $cita->cliente_id ?: auth()->id();
            $estilistaId = $cita->estilista_id;
        }

        if (!$estilistaId) {
            $primerEstilista = User::whereHas('role', function($q) { $q->where('slug', 'estilista'); })->first();
            $estilistaId = $primerEstilista ? $primerEstilista->id : (auth()->id() ?: 1);
        }

        $valoracion = Valoracion::create([
            'cita_id' => $citaId,
            'cliente_id' => $clienteId,
            'estilista_id' => $estilistaId,
            'estrellas' => $request->estrellas,
            'comentario' => $request->comentario,
            'fecha' => now(),
        ]);

        if (auth()->check()) {
            $this->logActivity('CREATE_REVIEW', "Valoración de {$request->estrellas} estrellas registrada", $valoracion->toArray());
        }

        return back()->with('success', '¡Muchas gracias por tu valoración y opinión!');
    }
}
