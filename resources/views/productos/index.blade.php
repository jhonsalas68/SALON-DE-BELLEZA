@extends('layouts.app')

@section('title', 'Productos - Salon Anita')

@section('header')
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-3xl font-extrabold text-gray-800 tracking-tight">@if(auth()->user()->hasPermission('manage_inventory')) Gestión de Inventario @else Nuestro Catálogo @endif</h2>
            <p class="text-gray-500 font-medium">@if(auth()->user()->hasPermission('manage_inventory')) Administra los suministros y stock del salón. @else Productos premium seleccionados especialmente para ti. @endif</p>
        </div>
        @if(auth()->user()->hasPermission('manage_inventory'))
        <a href="{{ route('productos.create') }}" class="bg-rose-500 hover:bg-rose-600 text-white px-6 py-3 rounded-2xl font-bold transition-all shadow-lg shadow-rose-100 flex items-center justify-center space-x-2">
            <i class="fas fa-plus text-xs"></i>
            <span>Nuevo Producto</span>
        </a>
        @endif
    </div>
@endsection

@section('content')
<div class="bg-white rounded-3xl shadow-sm border border-rose-50 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-rose-50/30">
                    <th class="p-5 text-xs font-black text-rose-400 uppercase tracking-widest border-b border-rose-50">Producto</th>
                    <th class="p-5 text-xs font-black text-rose-400 uppercase tracking-widest border-b border-rose-50 text-center">Disponibilidad</th>
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
                                <img src="{{ asset($producto->imagen) }}" class="w-12 h-12 rounded-xl object-cover shadow-sm border border-rose-100">
                            @else
                                <div class="w-12 h-12 rounded-xl bg-orange-100 flex items-center justify-center text-orange-600">
                                    <i class="fas fa-box text-xl"></i>
                                </div>
                            @endif
                            <div>
                                <p class="font-bold text-gray-800 leading-tight">{{ $producto->nombre }}</p>
                                <p class="text-[10px] text-gray-400 line-clamp-1 w-48 mt-0.5">{{ $producto->descripcion ?? 'Sin descripción' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="p-5 text-center">
                        @if($producto->stock > 0)
                            <span class="px-3 py-1 bg-emerald-100 text-emerald-600 rounded-full text-[10px] font-black uppercase">Disponible</span>
                        @else
                            <span class="px-3 py-1 bg-gray-100 text-gray-400 rounded-full text-[10px] font-black uppercase tracking-widest">Agotado</span>
                        @endif
                        @if(auth()->user()->hasPermission('manage_inventory') || auth()->user()->hasPermission('view_inventory'))
                            <p class="text-[10px] @if($producto->stock <= 5) text-rose-500 @else text-gray-400 @endif font-bold mt-1 uppercase">{{ $producto->stock }} en stock</p>
                        @endif
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
