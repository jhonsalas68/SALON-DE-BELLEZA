@extends('layouts.app')

@section('title', 'Roles y Permisos - Salón de Belleza Anita')

@section('header')
<div class="flex justify-between items-center mb-8">
    <div>
        <h1 class="text-3xl font-black text-gray-900 tracking-tight italic">Niveles de Acceso</h1>
        <p class="text-gray-500 font-medium">Define las responsabilidades detalladas para cada miembro.</p>
    </div>
    <a href="{{ route('roles.create') }}" class="bg-rose-500 hover:bg-rose-600 text-white px-6 py-3 rounded-2xl shadow-lg shadow-rose-100 transition-all font-bold flex items-center space-x-2 transform active:scale-95">
        <i class="fas fa-plus"></i>
        <span>Definir Nuevo Nivel</span>
    </a>
</div>
@endsection

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
    @foreach($roles as $role)
    <div class="group relative bg-white rounded-[2.5rem] p-1 border border-rose-50 shadow-sm hover:shadow-2xl transition-all duration-500 hover:-translate-y-2 select-none overflow-hidden">
        <!-- Background Decor -->
        <div class="absolute -top-10 -right-10 w-32 h-32 bg-rose-50/50 rounded-full blur-2xl group-hover:bg-rose-100 transition-colors duration-500"></div>
        
        <div class="relative p-8 flex flex-col h-full bg-white rounded-[2.3rem]">
            <div class="flex items-center justify-between mb-8">
                <div class="w-16 h-16 bg-gradient-to-br from-rose-400 to-rose-600 rounded-[1.5rem] flex items-center justify-center shadow-lg shadow-rose-100 group-hover:rotate-6 transition-transform duration-300">
                    <i class="fas fa-shield-alt text-white text-2xl"></i>
                </div>
                <div class="flex flex-col items-end">
                    <span class="px-4 py-1.5 bg-rose-50 text-rose-600 rounded-full text-[10px] font-black uppercase tracking-tighter">
                        {{ $role->users_count ?? $role->users->count() }} Miembros
                    </span>
                </div>
            </div>

            <h3 class="text-2xl font-black text-gray-900 mb-2 tracking-tight group-hover:text-rose-600 transition-colors italic">{{ $role->name }}</h3>
            <p class="text-gray-500 font-medium text-sm leading-relaxed mb-6 flex-grow">
                {{ $role->description ?? 'Sin descripción definida para este nivel de acceso.' }}
            </p>

            <div class="space-y-4 mb-8">
                <div class="flex items-center space-x-2">
                    <span class="w-1.5 h-1.5 bg-rose-400 rounded-full"></span>
                    <p class="text-[10px] font-black text-rose-300 uppercase tracking-widest">Capacidades Autorizadas</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    @forelse($role->permissions->take(6) as $p)
                        <span class="px-3 py-1.5 bg-rose-50/30 text-rose-600 rounded-xl text-[10px] font-bold border border-rose-100/50 transition-all hover:bg-rose-100">
                            <i class="fas fa-check text-rose-400 mr-1 text-[8px]"></i>
                            {{ $p->name }}
                        </span>
                    @empty
                        <span class="text-xs text-gray-400 italic">Acceso básico sin permisos adicionales</span>
                    @endforelse
                </div>
            </div>

            <div class="flex items-center space-x-3 pt-4 border-t border-rose-50">
                <a href="{{ route('roles.edit', $role) }}" class="flex-1 bg-gray-900 hover:bg-rose-600 text-white font-black py-4 rounded-2xl transition-all text-center text-sm shadow-xl shadow-gray-200">
                    Configurar
                </a>
                @if(!in_array($role->slug, ['administrador', 'cliente']))
                <form action="{{ route('roles.destroy', $role) }}" method="POST" class="inline" onsubmit="return confirm('¿Estás seguro de eliminar este acceso?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-14 h-14 flex items-center justify-center rounded-2xl bg-rose-50 text-rose-400 hover:bg-rose-500 hover:text-white transition-all duration-300">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection
