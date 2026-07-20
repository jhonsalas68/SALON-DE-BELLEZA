<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Traits\LogsActivity;

class ClienteController extends Controller
{
    use LogsActivity;

    public function index(Request $request)
    {
        // Buscar solo usuarios con rol 'cliente'
        $query = User::whereHas('role', function($q) {
            $q->where('slug', 'cliente');
        });

        // Aplicar filtro de búsqueda si existe en cualquier campo
        if ($request->filled('search')) {
            $search = trim($request->search);
            $searchLower = mb_strtolower($search, 'UTF-8');
            $query->where(function($q) use ($searchLower) {
                $q->whereRaw('LOWER(COALESCE(name, \'\')) LIKE ?', ["%{$searchLower}%"])
                  ->orWhereRaw('LOWER(COALESCE(nombre, \'\')) LIKE ?', ["%{$searchLower}%"])
                  ->orWhereRaw('LOWER(COALESCE(apellido, \'\')) LIKE ?', ["%{$searchLower}%"])
                  ->orWhereRaw('LOWER(COALESCE(email, \'\')) LIKE ?', ["%{$searchLower}%"])
                  ->orWhereRaw('LOWER(COALESCE(telefono, \'\')) LIKE ?', ["%{$searchLower}%"])
                  ->orWhereRaw('CAST(puntos AS text) LIKE ?', ["%{$searchLower}%"]);
            });
        }

        $clientes = $query->paginate(12);
        
        return view('clientes.index', compact('clientes'));
    }

    public function create()
    {
        return view('clientes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
        ]);

        $roleCliente = Role::where('slug', 'cliente')->first();

        $cliente = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make('cliente123'), // Contraseña por defecto
            'role_id' => $roleCliente->id,
            // 'phone' => $request->phone, // Si se añade a la tabla en el futuro
        ]);

        $this->logActivity('CREATE', "Cliente registrado: {$cliente->name}", ['email' => $cliente->email]);

        return redirect()->route('clientes.index')->with('success', 'Cliente registrado exitosamente.');
    }

    public function edit(User $cliente)
    {
        // Verificar que realmente sea un cliente
        if (!$cliente->hasRole('cliente')) {
            return redirect()->route('clientes.index')->with('error', 'El usuario seleccionado no es un cliente.');
        }

        return view('clientes.edit', compact('cliente'));
    }

    public function update(Request $request, User $cliente)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $cliente->id,
            'phone' => 'nullable|string|max:20',
        ]);

        $oldData = $cliente->only(['name', 'email']);
        
        $cliente->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        $this->logActivity('UPDATE', "Cliente actualizado: {$cliente->name}", [
            'old' => $oldData,
            'new' => $cliente->only(['name', 'email'])
        ]);

        return redirect()->route('clientes.index')->with('success', 'Cliente actualizado exitosamente.');
    }

    public function destroy(User $cliente)
    {
        if (!$cliente->hasRole('cliente')) {
            return redirect()->route('clientes.index')->with('error', 'El usuario seleccionado no es un cliente.');
        }

        $name = $cliente->name;
        $cliente->delete();

        $this->logActivity('DELETE', "Cliente eliminado: {$name}", []);

        return redirect()->route('clientes.index')->with('success', 'Cliente eliminado exitosamente.');
    }
}
