<?php

namespace App\Http\Controllers;

use App\Models\Servicio;
use Illuminate\Http\Request;
use App\Traits\LogsActivity;

class ServicioController extends Controller
{
    use LogsActivity;
    public function index()
    {
        $servicios = Servicio::all();
        return view('servicios.index', compact('servicios'));
    }

    public function create()
    {
        return view('servicios.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'duracion_minutos' => 'required|integer|min:5',
            'precio' => 'required|numeric|min:0',
        ]);

        $servicio = Servicio::create($request->all());

        if (auth()->check()) {
            $this->logActivity('CREATE', 'Servicio creado: ' . $servicio->nombre, $servicio->toArray());
        }

        return redirect()->route('servicios.index')->with('success', 'Servicio creado exitosamente.');
    }

    public function edit(Servicio $servicio)
    {
        return view('servicios.edit', compact('servicio'));
    }

    public function update(Request $request, Servicio $servicio)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'duracion_minutos' => 'required|integer|min:5',
            'precio' => 'required|numeric|min:0',
            'activo' => 'boolean'
        ]);

        $oldData = $servicio->toArray();
        $servicio->update($request->all());

        if (auth()->check()) {
            $this->logActivity('UPDATE', 'Servicio actualizado ID: ' . $servicio->id, [
                'old' => $oldData,
                'new' => $servicio->fresh()->toArray()
            ]);
        }

        return redirect()->route('servicios.index')->with('success', 'Servicio actualizado exitosamente.');
    }

    public function destroy(Servicio $servicio)
    {
        $servicioId = $servicio->id;
        $oldData = $servicio->toArray();
        $servicio->delete();

        if (auth()->check()) {
            $this->logActivity('DELETE', 'Servicio eliminado ID: ' . $servicioId, $oldData);
        }

        return redirect()->route('servicios.index')->with('success', 'Servicio eliminado exitosamente.');
    }
}
