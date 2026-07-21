@extends('layouts.app')

@section('title', 'Buscar Clientes - Salon Anita')

@section('header')
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-3xl font-extrabold text-gray-800 tracking-tight">Directorio de Clientes</h2>
            <p class="text-gray-500 font-medium">Encuentra a tus clientes rápidamente.</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="relative inline-block text-left" x-data="{ open: false }">
                <button @click="open = !open" type="button" class="bg-stone-800 hover:bg-stone-900 text-white px-5 py-3 rounded-2xl font-bold shadow-md transition-all flex items-center gap-2 text-sm">
                    <i class="fas fa-file-export text-amber-400"></i>
                    <span>Exportar</span>
                    <i class="fas fa-chevron-down text-xs"></i>
                </button>
                <div x-show="open" x-cloak style="display: none;" @click.away="open = false" class="origin-top-right absolute right-0 mt-2 w-48 rounded-2xl shadow-xl bg-white ring-1 ring-black ring-opacity-5 z-50 divide-y divide-gray-100 p-1">
                    <a href="{{ route('reports.export', ['modulo' => 'clientes', 'format' => 'excel']) }}" class="flex items-center space-x-2 px-4 py-2.5 text-xs font-bold text-emerald-700 hover:bg-emerald-50 rounded-xl transition-colors">
                        <i class="fas fa-file-excel text-emerald-500 text-sm"></i>
                        <span>Exportar Excel (.csv)</span>
                    </a>
                    <a href="{{ route('reports.export', ['modulo' => 'clientes', 'format' => 'pdf']) }}" class="flex items-center space-x-2 px-4 py-2.5 text-xs font-bold text-rose-700 hover:bg-rose-50 rounded-xl transition-colors">
                        <i class="fas fa-file-pdf text-rose-500 text-sm"></i>
                        <span>Reporte PDF / Imprimir</span>
                    </a>
                </div>
            </div>
            <a href="{{ route('clientes.create') }}" class="bg-rose-500 hover:bg-rose-600 text-white px-6 py-3 rounded-2xl font-bold shadow-lg shadow-rose-200 transition-all flex items-center gap-2">
                <i class="fas fa-plus"></i> Registrar Cliente
            </a>
        </div>
    </div>
@endsection

@section('content')
<div class="bg-white rounded-3xl shadow-sm border border-rose-50 p-6 mb-6">
    <form action="{{ route('clientes.index') }}" method="GET" class="flex flex-col md:flex-row items-center gap-4">
        <div class="flex-1 relative w-full">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <i class="fas fa-search text-gray-400"></i>
            </div>
            <input type="text" name="search" value="{{ request('search') }}" class="w-full pl-12 pr-4 py-3 bg-gray-50 border border-gray-200 text-gray-800 rounded-2xl focus:ring-2 focus:ring-rose-200 focus:border-rose-400 transition-all font-medium" placeholder="Buscar por nombre o correo electrónico...">
        </div>
        <div class="flex items-center gap-3 w-full md:w-auto">
            <button type="submit" class="flex-1 md:flex-none bg-rose-500 hover:bg-rose-600 text-white px-8 py-3 rounded-2xl font-bold shadow-lg shadow-rose-200 transition-all flex items-center justify-center space-x-2">
                <span>Buscar</span>
            </button>
            @if(request('search'))
                <a href="{{ route('clientes.index') }}" class="px-6 py-3 rounded-2xl font-bold text-gray-500 hover:bg-gray-100 transition-colors text-center">Limpiar</a>
            @endif
        </div>
    </form>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
    @forelse($clientes as $cliente)
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-rose-50 hover:shadow-md hover:border-rose-200 transition-all relative overflow-hidden group">
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-rose-400 to-orange-400 opacity-0 group-hover:opacity-100 transition-opacity"></div>
            <div class="flex flex-col items-center text-center">
                <div class="w-20 h-20 bg-rose-50 text-rose-500 rounded-full flex items-center justify-center text-3xl font-extrabold mb-4 shadow-sm border border-rose-100">
                    {{ strtoupper(substr($cliente->name ?? $cliente->email, 0, 1)) }}
                </div>
                <h3 class="text-lg font-extrabold text-gray-800 leading-tight mb-1 line-clamp-1" title="{{ $cliente->name }}">{{ $cliente->name ?? 'Sin Nombre' }}</h3>
                <p class="text-xs font-medium text-gray-500 mb-2 truncate w-full" title="{{ $cliente->email }}">{{ $cliente->email }}</p>
                
                <div class="mb-4">
                    <span class="inline-flex items-center px-3 py-1 bg-amber-50 text-amber-700 border border-amber-200/60 rounded-full text-xs font-black shadow-xs">
                        <i class="fas fa-gem text-amber-500 mr-1.5"></i> {{ $cliente->puntos ?? 0 }} Pts Fidelización
                    </span>
                </div>
                
                <div class="w-full flex justify-center space-x-2 border-t border-gray-50 pt-4 mt-2">
                    <a href="{{ route('citas.create', ['cliente_id' => $cliente->id]) }}" class="flex-1 py-2.5 bg-gray-50 hover:bg-rose-50 text-gray-600 hover:text-rose-600 rounded-xl font-bold text-xs transition-colors flex items-center justify-center space-x-2">
                        <i class="fas fa-calendar-plus"></i>
                        <span>Agendar Cita</span>
                    </a>
                    <a href="{{ route('clientes.edit', $cliente->id) }}" class="p-2.5 bg-gray-50 hover:bg-amber-50 text-gray-600 hover:text-amber-600 rounded-xl transition-colors" title="Editar">
                        <i class="fas fa-edit"></i>
                    </a>
                    <form action="{{ route('clientes.destroy', $cliente->id) }}" method="POST" class="inline" onsubmit="return confirm('¿Estás seguro de eliminar este cliente?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-2.5 bg-gray-50 hover:bg-red-50 text-gray-600 hover:text-red-600 rounded-xl transition-colors" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="col-span-full py-16 text-center text-gray-400 bg-white rounded-3xl border border-rose-50 shadow-sm">
            <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-user-slash text-4xl text-gray-300"></i>
            </div>
            <p class="font-bold text-xl text-gray-600 mb-1">No se encontraron clientes</p>
            <p class="text-sm">Intenta con otros términos de búsqueda.</p>
        </div>
    @endforelse
</div>

<div class="mt-8">
    {{ $clientes->links() }}
</div>
@endsection
