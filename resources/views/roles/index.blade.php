@extends('layouts.app')

@section('title', 'Roles y Permisos - Salón de Belleza Anita')

@section('header')
<div class="flex justify-between items-center mb-8">
    <div>
        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Roles y Permisos</h1>
        <p class="text-gray-500 font-medium">Define niveles de acceso y responsabilidades.</p>
    </div>
    <a href="{{ route('roles.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl shadow-lg shadow-indigo-200 transition-all font-bold flex items-center space-x-2 transform active:scale-95">
        <i class="fas fa-plus"></i>
        <span>Definir Nuevo Rol</span>
    </a>
</div>
@endsection

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @foreach($roles as $role)
    <div class="bg-gray-50 border border-gray-100 rounded-[2rem] p-8 flex flex-col hover:shadow-xl transition-all duration-300 group">
        <div class="flex items-center justify-between mb-6">
            <div class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center shadow-sm group-hover:bg-indigo-600 group-hover:text-white transition-all duration-300">
                <i class="fas fa-user-shield text-xl"></i>
            </div>
            <div class="text-right">
                <span class="px-3 py-1 bg-white border border-gray-100 rounded-lg text-[10px] font-black text-gray-400 uppercase tracking-widest">
                    {{ $role->users_count ?? $role->users->count() }} Usuarios
                </span>
            </div>
        </div>

        <h3 class="text-xl font-black text-gray-800 mb-2">{{ $role->name }}</h3>
        <p class="text-sm text-gray-500 font-medium mb-6 flex-grow leading-relaxed">{{ $role->description }}</p>

        <div class="mb-8">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Permisos Clave</p>
            <div class="flex flex-wrap gap-2">
                @forelse($role->permissions->take(5) as $p)
                    <span class="px-3 py-1 bg-white border border-gray-100 rounded-lg text-[10px] font-bold text-gray-600">{{ $p->name }}</span>
                @empty
                    <span class="text-xs text-gray-400 italic font-medium">Sin permisos específicos</span>
                @endforelse
                @if($role->permissions->count() > 5)
                    <span class="px-3 py-1 bg-white border border-gray-100 rounded-lg text-[10px] font-bold text-gray-400">+{{ $role->permissions->count() - 5 }} más</span>
                @endif
            </div>
        </div>

        <div class="flex items-center space-x-3">
            <a href="{{ route('roles.edit', $role) }}" class="flex-1 bg-white border border-gray-200 hover:border-indigo-600 hover:text-indigo-600 text-gray-700 font-bold py-3 rounded-xl transition-all text-center">
                Configurar
            </a>
            @if(!in_array($role->slug, ['administrador', 'cliente']))
            <form action="{{ route('roles.destroy', $role) }}" method="POST" class="inline" onsubmit="return confirm('¿Estás seguro de eliminar este rol?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-12 h-12 flex items-center justify-center rounded-xl bg-red-50 text-red-400 hover:bg-red-500 hover:text-white transition-all">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </form>
            @endif
        </div>
    </div>
    @endforeach
</div>
@endsection
