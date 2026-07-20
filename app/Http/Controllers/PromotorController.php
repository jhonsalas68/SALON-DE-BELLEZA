<?php

namespace App\Http\Controllers;

use App\Models\Promotor;
use Illuminate\Http\Request;
use App\Traits\LogsActivity;

class PromotorController extends Controller
{
    use LogsActivity;
    public function index(Request $request)
    {
        $query = Promotor::query();

        if ($request->filled('search')) {
            $search = trim($request->search);
            $searchLower = mb_strtolower($search, 'UTF-8');
            $query->where(function($q) use ($searchLower) {
                $q->whereRaw('LOWER(COALESCE(nombre, \'\')) LIKE ?', ["%{$searchLower}%"])
                  ->orWhereRaw('LOWER(COALESCE(empresa, \'\')) LIKE ?', ["%{$searchLower}%"])
                  ->orWhereRaw('LOWER(COALESCE(telefono, \'\')) LIKE ?', ["%{$searchLower}%"])
                  ->orWhereRaw('LOWER(COALESCE(notas, \'\')) LIKE ?', ["%{$searchLower}%"]);
            });
        }

        $promotores = $query->latest()->get();
        return view('promotores.index', compact('promotores'));
    }

    public function create()
    {
        return view('promotores.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'empresa' => 'nullable|string|max:255',
            'notas' => 'nullable|string',
         
        ]);

        Promotor::create($request->all());

        $this->logActivity('CREATE', "Proveedor creado: {$request->nombre}", $request->all());

        return redirect()->route('promotores.index')->with('success', 'Promotor creado exitosamente.');
    }

    public function edit(Promotor $promotore)
    {
        // Laravel uses 'promotore' as singular for 'promotores' route segment sometimes
        return view('promotores.edit', ['promotor' => $promotore]);
    }

    public function update(Request $request, Promotor $promotore)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'empresa' => 'nullable|string|max:255',
            'notas' => 'nullable|string',
        
        ]);

        $oldData = $promotore->toArray();
        $promotore->update($request->all());

        $this->logActivity('UPDATE', "Proveedor actualizado: {$request->nombre}", [
            'old' => $oldData,
            'new' => $promotore->fresh()->toArray()
        ]);

        return redirect()->route('promotores.index')->with('success', 'Promotor actualizado exitosamente.');
    }

    public function destroy(Promotor $promotore)
    {
        $promotorData = $promotore->toArray();
        $nombre = $promotore->nombre;
        $promotore->delete();
        
        $this->logActivity('DELETE', "Proveedor eliminado: {$nombre}", $promotorData);
        
        return redirect()->route('promotores.index')->with('success', 'Promotor eliminado exitosamente.');
    }
}
