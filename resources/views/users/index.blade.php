@extends('layouts.app')

@section('title', 'Usuarios - Salón de Belleza Anita')

@section('header')
<div class="flex justify-between items-center mb-8">
    <div>
        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Gestión de Usuarios</h1>
        <p class="text-gray-500 font-medium">Administra el personal y clientes del salón.</p>
    </div>
    <a href="{{ route('users.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl shadow-lg shadow-indigo-200 transition-all font-bold flex items-center space-x-2 transform active:scale-95">
        <i class="fas fa-plus"></i>
        <span>Nuevo Usuario</span>
    </a>
</div>
@endsection

@section('content')
<div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/50">
                    <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-widest">Información de Usuario</th>
                    <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-widest text-center">Rol Asignado</th>
                    <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-widest">Fecha de Registro</th>
                    <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-widest text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($users as $u)
                <tr class="hover:bg-indigo-50/30 transition duration-150 group">
                    <td class="px-8 py-6">
                        <div class="flex items-center space-x-4">
                            <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold">
                                {{ substr($u->email, 0, 1) }}
                            </div>
                            <div>
                                <p class="text-sm font-bold text-gray-800">{{ $u->email }}</p>
                                <p class="text-[10px] text-gray-400 font-medium">ID: #{{ str_pad($u->id, 5, '0', STR_PAD_LEFT) }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-8 py-6 text-center">
                        <span class="px-4 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-widest
                            @if($u->role->slug == 'administrador') bg-purple-100 text-purple-700 
                            @elseif($u->role->slug == 'recepcionista') bg-blue-100 text-blue-700 
                            @elseif($u->role->slug == 'estilista') bg-pink-100 text-pink-700 
                            @else bg-gray-100 text-gray-600 @endif">
                            {{ $u->role->name ?? 'Invitado' }}
                        </span>
                    </td>
                    <td class="px-8 py-6">
                        <div class="text-sm text-gray-500 font-medium">
                            {{ $u->created_at->format('d M, Y') }}
                            <span class="block text-[10px] text-gray-300">{{ $u->created_at->diffForHumans() }}</span>
                        </div>
                    </td>
                    <td class="px-8 py-6 text-right">
                        <div class="flex justify-end items-center space-x-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <a href="{{ route('users.edit', $u) }}" class="w-9 h-9 flex items-center justify-center rounded-xl bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-all">
                                <i class="fas fa-edit text-sm"></i>
                            </a>
                            <form action="{{ route('users.destroy', $u) }}" method="POST" class="inline" onsubmit="return confirm('¿Estás seguro de eliminar este usuario?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-9 h-9 flex items-center justify-center rounded-xl bg-red-50 text-red-600 hover:bg-red-600 hover:text-white transition-all">
                                    <i class="fas fa-trash text-sm"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
    <div class="px-8 py-6 border-t border-gray-50">
        {{ $users->links() }}
    </div>
    @endif
</div>
@endsection
