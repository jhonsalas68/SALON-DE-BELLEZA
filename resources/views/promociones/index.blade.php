@extends('layouts.app')

@section('title', 'Promociones - Salon Anita')

@section('header')
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-3xl font-extrabold text-gray-800 tracking-tight">Gestión de Promociones</h2>
            <p class="text-gray-500 font-medium">Configura descuentos para servicios y productos del salón.</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="relative inline-block text-left" x-data="{ open: false }">
                <button @click="open = !open" type="button" class="bg-stone-800 hover:bg-stone-900 text-white px-5 py-3 rounded-2xl font-bold shadow-md transition-all flex items-center gap-2 text-sm">
                    <i class="fas fa-file-export text-amber-400"></i>
                    <span>Exportar</span>
                    <i class="fas fa-chevron-down text-xs"></i>
                </button>
                <div x-show="open" @click.away="open = false" class="origin-top-right absolute right-0 mt-2 w-48 rounded-2xl shadow-xl bg-white ring-1 ring-black ring-opacity-5 z-50 divide-y divide-gray-100 p-1">
                    <a href="{{ route('reports.export', ['modulo' => 'promociones', 'format' => 'excel']) }}" class="flex items-center space-x-2 px-4 py-2.5 text-xs font-bold text-emerald-700 hover:bg-emerald-50 rounded-xl transition-colors">
                        <i class="fas fa-file-excel text-emerald-500 text-sm"></i>
                        <span>Exportar Excel (.csv)</span>
                    </a>
                    <a href="{{ route('reports.export', ['modulo' => 'promociones', 'format' => 'pdf']) }}" class="flex items-center space-x-2 px-4 py-2.5 text-xs font-bold text-rose-700 hover:bg-rose-50 rounded-xl transition-colors">
                        <i class="fas fa-file-pdf text-rose-500 text-sm"></i>
                        <span>Reporte PDF / Imprimir</span>
                    </a>
                </div>
            </div>
            @if(auth()->user()->hasRole('administrador'))
            <a href="{{ route('promociones.create') }}" class="bg-rose-500 hover:bg-rose-600 text-white px-6 py-3 rounded-2xl font-bold shadow-lg shadow-rose-200 transition-all flex items-center gap-2">
                <i class="fas fa-plus"></i> Crear Nueva Promoción
            </a>
            @endif
        </div>
    </div>
@endsection

@section('content')
<div class="bg-white rounded-3xl shadow-sm border border-rose-50 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                    <th class="p-4 font-bold border-b border-gray-100 rounded-tl-2xl">Promoción</th>
                    <th class="p-4 font-bold border-b border-gray-100">Descuento</th>
                    <th class="p-4 font-bold border-b border-gray-100">Aplica A</th>
                    <th class="p-4 font-bold border-b border-gray-100">Vigencia</th>
                    <th class="p-4 font-bold border-b border-gray-100">Estado</th>
                    <th class="p-4 font-bold border-b border-gray-100 rounded-tr-2xl text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="text-sm">
                @forelse($promociones as $promo)
                <tr class="hover:bg-rose-50/30 transition-colors group">
                    <td class="p-4 border-b border-gray-50">
                        <span class="font-bold text-gray-800">{{ $promo->nombre }}</span><br>
                        <span class="text-gray-500 text-xs">{{ Str::limit($promo->descripcion, 50) ?: 'Sin descripción' }}</span>
                    </td>
                    <td class="p-4 border-b border-gray-50">
                        <span class="px-2.5 py-1 bg-rose-100 text-rose-700 rounded-lg text-xs font-black">
                            {{ number_format($promo->descuento_porcentaje, 0) }}% OFF
                        </span>
                    </td>
                    <td class="p-4 border-b border-gray-50 font-bold text-gray-700">
                        @if($promo->servicio)
                            <span class="text-rose-500"><i class="fas fa-cut mr-1.5"></i> Servicio: {{ $promo->servicio->nombre }}</span>
                        @elseif($promo->producto)
                            <span class="text-indigo-500"><i class="fas fa-box-open mr-1.5"></i> Producto: {{ $promo->producto->nombre }}</span>
                        @else
                            <span class="text-gray-500">General</span>
                        @endif
                    </td>
                    <td class="p-4 border-b border-gray-50 text-gray-600">
                        <span class="text-xs font-semibold">Desde: {{ $promo->fecha_inicio->format('d M, Y') }}</span><br>
                        <span class="text-xs font-semibold">Hasta: {{ $promo->fecha_fin->format('d M, Y') }}</span>
                    </td>
                    <td class="p-4 border-b border-gray-50">
                        @if($promo->esta_vigente)
                            <span class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-lg text-xs font-bold">Activa y Vigente</span>
                        @elseif(!$promo->activo)
                            <span class="px-3 py-1 bg-gray-100 text-gray-500 rounded-lg text-xs font-bold">Desactivada</span>
                        @else
                            <span class="px-3 py-1 bg-amber-100 text-amber-700 rounded-lg text-xs font-bold">Expirada</span>
                        @endif
                    </td>
                    <td class="p-4 border-b border-gray-50 text-right space-x-2">
                        @if(auth()->user()->hasRole('administrador'))
                        <a href="{{ route('promociones.edit', $promo->id) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-gray-50 text-gray-500 hover:bg-amber-50 hover:text-amber-500 transition-colors" title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('promociones.destroy', $promo->id) }}" method="POST" class="inline" onsubmit="return confirm('¿Eliminar esta promoción?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-gray-50 text-gray-500 hover:bg-red-50 hover:text-red-500 transition-colors">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                        @else
                        <span class="text-gray-400 text-xs">Sin permisos</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="p-8 text-center text-gray-400">
                        No hay promociones registradas.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-6">
    {{ $promociones->links() }}
</div>
@endsection
