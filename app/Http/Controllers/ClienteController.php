<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        // Buscar solo usuarios con rol 'cliente'
        $query = User::whereHas('role', function($q) {
            $q->where('slug', 'cliente');
        });

        // Aplicar filtro de búsqueda si existe
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $clientes = $query->paginate(12);
        
        return view('clientes.index', compact('clientes'));
    }
}
