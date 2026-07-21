@extends('layouts.app')

@section('title', 'Productos - Salon Anita')

@section('header')
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-3xl font-extrabold text-gray-800 tracking-tight">@if(auth()->user()->hasPermission('manage_inventory')) Gestión de Inventario @else Nuestro Catálogo @endif</h2>
            <p class="text-gray-500 font-medium">@if(auth()->user()->hasPermission('manage_inventory')) Administra los suministros y stock del salón. @else Productos premium seleccionados especialmente para ti. @endif</p>
        </div>
        <div class="flex items-center space-x-3">
            <div class="relative inline-block text-left" x-data="{ open: false }">
                <button @click="open = !open" type="button" class="bg-stone-800 hover:bg-stone-900 text-white px-5 py-3 rounded-2xl font-bold shadow-md transition-all flex items-center gap-2 text-sm">
                    <i class="fas fa-file-export text-amber-400"></i>
                    <span>Exportar</span>
                    <i class="fas fa-chevron-down text-xs"></i>
                </button>
                <div x-show="open" @click.away="open = false" class="origin-top-right absolute right-0 mt-2 w-48 rounded-2xl shadow-xl bg-white ring-1 ring-black ring-opacity-5 z-50 divide-y divide-gray-100 p-1">
                    <a href="{{ route('reports.export', ['modulo' => 'productos', 'format' => 'excel']) }}" class="flex items-center space-x-2 px-4 py-2.5 text-xs font-bold text-emerald-700 hover:bg-emerald-50 rounded-xl transition-colors">
                        <i class="fas fa-file-excel text-emerald-500 text-sm"></i>
                        <span>Exportar Excel (.csv)</span>
                    </a>
                    <a href="{{ route('reports.export', ['modulo' => 'productos', 'format' => 'pdf']) }}" class="flex items-center space-x-2 px-4 py-2.5 text-xs font-bold text-rose-700 hover:bg-rose-50 rounded-xl transition-colors">
                        <i class="fas fa-file-pdf text-rose-500 text-sm"></i>
                        <span>Reporte PDF / Imprimir</span>
                    </a>
                </div>
            </div>
            @if(auth()->user()->hasPermission('manage_inventory'))
            <a href="{{ route('productos.create') }}" class="bg-rose-500 hover:bg-rose-600 text-white px-6 py-3 rounded-2xl font-bold transition-all shadow-lg shadow-rose-100 flex items-center justify-center space-x-2">
                <i class="fas fa-plus text-xs"></i>
                <span>Nuevo Producto</span>
            </a>
            @endif
        </div>
    </div>

    <!-- Buscador y Filtros -->
    <div class="mt-6 bg-white p-4 rounded-3xl border border-rose-50 shadow-sm">
        <form action="{{ route('productos.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="relative">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nombre o código..." 
                    class="w-full pl-10 pr-4 py-3 bg-rose-50/30 border border-rose-100 rounded-xl focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 outline-none text-sm">
            </div>
            
            <select name="stock_status" class="w-full px-4 py-3 bg-rose-50/30 border border-rose-100 rounded-xl focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 outline-none text-sm appearance-none cursor-pointer">
                <option value="">Estado de Stock (Todos)</option>
                <option value="bajo" {{ request('stock_status') == 'bajo' ? 'selected' : '' }}>Stock Bajo</option>
                <option value="critico" {{ request('stock_status') == 'critico' ? 'selected' : '' }}>Stock Crítico</option>
            </select>

            <select name="vencimiento" class="w-full px-4 py-3 bg-rose-50/30 border border-rose-100 rounded-xl focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 outline-none text-sm appearance-none cursor-pointer">
                <option value="">Vencimiento (Cualquiera)</option>
                <option value="proximo" {{ request('vencimiento') == 'proximo' ? 'selected' : '' }}>Próximos a vencer</option>
            </select>

            <div class="flex space-x-2">
                <button type="submit" class="flex-1 bg-gray-800 hover:bg-gray-900 text-white font-bold py-3 rounded-xl transition-all text-sm">
                    Filtrar
                </button>
                <a href="{{ route('productos.index') }}" class="px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-500 rounded-xl transition-all flex items-center justify-center">
                    <i class="fas fa-sync-alt"></i>
                </a>
            </div>
        </form>
    </div>
@endsection

@section('content')
<div class="bg-white rounded-3xl shadow-sm border border-rose-50 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-rose-50/30">
                    <th class="p-5 text-xs font-black text-rose-400 uppercase tracking-widest border-b border-rose-50">Producto y Código</th>
                    <th class="p-5 text-xs font-black text-rose-400 uppercase tracking-widest border-b border-rose-50 text-center">Caducidad</th>
                    <th class="p-5 text-xs font-black text-rose-400 uppercase tracking-widest border-b border-rose-50 text-center">Estado Stock</th>
                    @if(auth()->user()->hasPermission('manage_inventory'))
                    <th class="p-5 text-xs font-black text-rose-400 uppercase tracking-widest border-b border-rose-50">Costo (Compra)</th>
                    @endif
                    <th class="p-5 text-xs font-black text-rose-400 uppercase tracking-widest border-b border-rose-50">Precio</th>
                    @if(auth()->user()->hasPermission('manage_inventory'))
                    <th class="p-5 text-xs font-black text-rose-400 uppercase tracking-widest border-b border-rose-50 text-right">Acciones</th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-rose-50">
                @forelse($productos as $producto)
                <tr class="hover:bg-rose-50/10 transition-colors">
                    <td class="p-5">
                        <div class="flex items-center space-x-3">
                            @if($producto->imagen)
                                <img src="{{ \Illuminate\Support\Str::startsWith($producto->imagen, 'http') ? $producto->imagen : asset($producto->imagen) }}" class="w-12 h-12 rounded-xl object-cover shadow-sm border border-rose-100">
                            @else
                                <div class="w-12 h-12 rounded-xl bg-orange-100 flex items-center justify-center text-orange-600">
                                    <i class="fas fa-box text-xl"></i>
                                </div>
                            @endif
                            <div>
                                <p class="font-bold text-gray-800 leading-tight">{{ $producto->nombre }}</p>
                                <p class="text-[9px] font-black text-rose-400 uppercase tracking-widest">{{ $producto->codigo ?? 'SIN CÓDIGO' }}</p>
                                <p class="text-[10px] text-gray-400 line-clamp-1 w-48 mt-0.5">{{ $producto->descripcion ?? 'Sin descripción' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="p-5 text-center">
                        @if($producto->fecha_caducidad)
                            <p class="text-xs font-bold @if(\Carbon\Carbon::parse($producto->fecha_caducidad)->isPast()) text-rose-500 @else text-gray-600 @endif">
                                {{ \Carbon\Carbon::parse($producto->fecha_caducidad)->format('d/m/Y') }}
                            </p>
                        @else
                            <p class="text-xs text-gray-300 italic">N/A</p>
                        @endif
                    </td>
                    <td class="p-5 text-center">
                        @php
                            $statusClass = 'bg-emerald-100 text-emerald-600';
                            $statusText = 'Normal';
                            if ($producto->stock <= $producto->stock_minimo) {
                                $statusClass = 'bg-red-100 text-red-600 animate-pulse';
                                $statusText = 'Crítico';
                            } elseif ($producto->stock <= ($producto->stock_minimo + 5)) {
                                $statusClass = 'bg-amber-100 text-amber-600';
                                $statusText = 'Bajo';
                            }
                        @endphp
                        <span class="px-3 py-1 {{ $statusClass }} rounded-full text-[10px] font-black uppercase tracking-wider">
                            {{ $statusText }}
                        </span>
                        <p class="text-[10px] font-bold mt-1 text-gray-500 uppercase">
                            {{ $producto->stock }} / <span class="text-gray-300">mín. {{ $producto->stock_minimo }}</span>
                        </p>
                    </td>
                    @if(auth()->user()->hasPermission('manage_inventory'))
                    <td class="p-5">
                        <p class="text-xs text-gray-400 font-bold">Bs. {{ number_format($producto->precio_compra, 2) }}</p>
                    </td>
                    @endif
                    <td class="p-5">
                        <p class="font-bold text-rose-600 text-lg">Bs. {{ number_format($producto->precio_venta, 2) }}</p>
                    </td>
                    @if(auth()->user()->hasPermission('manage_inventory'))
                    <td class="p-5 text-right">
                        <div class="flex justify-end space-x-2">
                            <a href="{{ route('productos.edit', $producto) }}" class="p-2 text-blue-500 hover:bg-blue-50 rounded-lg transition-colors" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('productos.destroy', $producto) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este producto?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-rose-500 hover:bg-rose-50 rounded-lg transition-colors" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                    @endif
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="p-10 text-center text-gray-400">
                        <i class="fas fa-shopping-bag text-4xl mb-3 block"></i>
                        <p class="font-medium">No hay productos registrados.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
