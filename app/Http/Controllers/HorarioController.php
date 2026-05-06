<?php

namespace App\Http\Controllers;

use App\Models\Horario;
use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\LogsActivity;

class HorarioController extends Controller
{
    use LogsActivity;
    public function index()
    {
        $horarios = Horario::with('user')->get();
        return view('horarios.index', compact('horarios'));
    }

    public function create()
    {
        $users = User::whereHas('role', function($q) {
            $q->whereIn('slug', ['estilista', 'recepcionista']);
        })->get();
        
        return view('horarios.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'dia_semana' => 'required|string',
            'hora_inicio' => 'required',
            'hora_fin' => 'required|after:hora_inicio',
        ]);

        $horario = Horario::create($request->all());

        if (auth()->check()) {
            $this->logActivity('CREATE', 'Horario creado para usuario ID: ' . $horario->user_id, $horario->toArray());
        }

        return redirect()->route('horarios.index')->with('success', 'Horario creado exitosamente.');
    }

    public function edit(Horario $horario)
    {
        $users = User::whereHas('role', function($q) {
            $q->whereIn('slug', ['estilista', 'recepcionista']);
        })->get();

        return view('horarios.edit', compact('horario', 'users'));
    }

    public function update(Request $request, Horario $horario)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'dia_semana' => 'required|string',
            'hora_inicio' => 'required',
            'hora_fin' => 'required|after:hora_inicio',
            'activo' => 'boolean'
        ]);

        $oldData = $horario->toArray();
        $horario->update($request->all());

        if (auth()->check()) {
            $this->logActivity('UPDATE', 'Horario actualizado ID: ' . $horario->id, [
                'old' => $oldData,
                'new' => $horario->fresh()->toArray()
            ]);
        }

        return redirect()->route('horarios.index')->with('success', 'Horario actualizado exitosamente.');
    }

    public function destroy(Horario $horario)
    {
        $horarioId = $horario->id;
        $oldData = $horario->toArray();
        $horario->delete();

        if (auth()->check()) {
            $this->logActivity('DELETE', 'Horario eliminado ID: ' . $horarioId, $oldData);
        }

        return redirect()->route('horarios.index')->with('success', 'Horario eliminado exitosamente.');
    }
}
