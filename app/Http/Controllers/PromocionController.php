<?php

namespace App\Http\Controllers;

use App\Models\Promocion;
use App\Models\Servicio;
use App\Models\Producto;
use Illuminate\Http\Request;
use App\Traits\LogsActivity;

class PromocionController extends Controller
{
    use LogsActivity;

    public function index()
    {
        $promociones = Promocion::with(['servicio', 'producto'])
            ->orderBy('fecha_fin', 'desc')
            ->paginate(15);
            
        return view('promociones.index', compact('promociones'));
    }

    public function create()
    {
        $servicios = Servicio::where('activo', true)->get();
        $productos = Producto::all();
        return view('promociones.create', compact('servicios', 'productos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'descuento_porcentaje' => 'required|numeric|min:0|max:100',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'activo' => 'boolean',
            'servicio_id' => 'nullable|exists:servicios,id',
            'producto_id' => 'nullable|exists:productos,id',
        ]);

        $data = $request->all();
        $data['activo'] = $request->has('activo');

        
        if ($request->filled('servicio_id') && $request->filled('producto_id')) {
            return back()->withInput()->with('error', 'Una promoción debe ser para un Servicio O para un Producto, no para ambos a la vez.');
        }

        $promocion = Promocion::create($data);

        $this->logActivity('CREATE', "Promoción creada: {$promocion->nombre}", $promocion->toArray());

        return redirect()->route('promociones.index')->with('success', 'Promoción creada exitosamente.');
    }

    public function edit(Promocion $promocion)
    {
        $servicios = Servicio::where('activo', true)->get();
        $productos = Producto::all();
        return view('promociones.edit', compact('promocion', 'servicios', 'productos'));
    }

    public function update(Request $request, Promocion $promocion)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'descuento_porcentaje' => 'required|numeric|min:0|max:100',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'activo' => 'boolean',
            'servicio_id' => 'nullable|exists:servicios,id',
            'producto_id' => 'nullable|exists:productos,id',
        ]);

        $data = $request->all();
        $data['activo'] = $request->has('activo');

        if ($request->filled('servicio_id') && $request->filled('producto_id')) {
            return back()->withInput()->with('error', 'Una promoción debe ser para un Servicio O para un Producto, no para ambos a la vez.');
        }

        $oldData = $promocion->toArray();
        $promocion->update($data);

        $this->logActivity('UPDATE', "Promoción actualizada ID: {$promocion->id}", [
            'old' => $oldData,
            'new' => $promocion->fresh()->toArray()
        ]);

        return redirect()->route('promociones.index')->with('success', 'Promoción actualizada exitosamente.');
    }

    public function destroy(Promocion $promocion)
    {
        $nombre = $promocion->nombre;
        $id = $promocion->id;
        $promocion->delete();

        $this->logActivity('DELETE', "Promoción eliminada ID: {$id} ({$nombre})", []);

        return redirect()->route('promociones.index')->with('success', 'Promoción eliminada exitosamente.');
    }
}
