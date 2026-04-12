@extends('layouts.app')

@section('title', 'Usuarios - Salón de Belleza Anita')

@section('header')
<div class="flex justify-between items-center mb-8">
    <div>
        <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Usuarios</h1>
        <p class="text-gray-500 text-sm">Lista de personal del salón.</p>
    </div>
    <a href="{{ route('users.create') }}" class="bg-rose-500 hover:bg-rose-600 text-white px-5 py-2.5 rounded-xl transition-all font-bold flex items-center space-x-2 text-sm shadow-sm">
        <i class="fas fa-plus"></i>
        <span>Nuevo Usuario</span>
    </a>
</div>
@endsection

@section('content')
<div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/50 border-b border-gray-100">
                    <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Email</th>
                    <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-center">Rol</th>
                    <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Registrado</th>
                    <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($users as $u)
                <tr class="hover:bg-gray-50/50 transition">
                    <td class="px-6 py-4">
                        <p class="text-sm font-semibold text-gray-700">{{ $u->email }}</p>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-3 py-1 rounded-lg text-[9px] font-bold uppercase tracking-wider
                            @if($u->role->slug == 'administrador') bg-rose-100 text-rose-700
                            @elseif($u->role->slug == 'recepcionista') bg-blue-100 text-blue-700 
                            @elseif($u->role->slug == 'estilista') bg-orange-100 text-orange-700 
                            @else bg-gray-100 text-gray-600 @endif">
                            {{ $u->role->name ?? 'Invitado' }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-xs text-gray-500">{{ $u->created_at->format('d/m/Y') }}</p>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end space-x-2">
                            <a href="{{ route('users.edit', $u) }}" class="text-gray-400 hover:text-rose-600 transition">
                                <i class="fas fa-edit text-sm"></i>
                            </a>
                            <form action="{{ route('users.destroy', $u) }}" method="POST" class="inline" onsubmit="return confirm('¿Eliminar usuario?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-gray-400 hover:text-red-500 transition">
                                    <i class="fas fa-trash-alt text-sm"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
