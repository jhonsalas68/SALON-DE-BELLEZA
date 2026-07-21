@extends('layouts.app')

@section('title', 'Ventas de Productos - Salon Anita')

@section('header')
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-3xl font-extrabold text-gray-800 tracking-tight">Historial de Ventas</h2>
            <p class="text-gray-500 font-medium">Visualiza y registra ventas de productos.</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="relative inline-block text-left" x-data="{ open: false }">
                <button @click="open = !open" type="button" class="bg-stone-800 hover:bg-stone-900 text-white px-5 py-3 rounded-2xl font-bold shadow-md transition-all flex items-center gap-2 text-sm">
                    <i class="fas fa-file-export text-amber-400"></i>
                    <span>Exportar</span>
                    <i class="fas fa-chevron-down text-xs"></i>
                </button>
                <div x-show="open" x-cloak style="display: none;" @click.away="open = false" class="origin-top-right absolute right-0 mt-2 w-48 rounded-2xl shadow-xl bg-white ring-1 ring-black ring-opacity-5 z-50 divide-y divide-gray-100 p-1">
                    <a href="{{ route('reports.export', ['modulo' => 'ventas', 'format' => 'excel']) }}" class="flex items-center space-x-2 px-4 py-2.5 text-xs font-bold text-emerald-700 hover:bg-emerald-50 rounded-xl transition-colors">
                        <i class="fas fa-file-excel text-emerald-500 text-sm"></i>
                        <span>Exportar Excel (.csv)</span>
                    </a>
                    <a href="{{ route('reports.export', ['modulo' => 'ventas', 'format' => 'pdf']) }}" class="flex items-center space-x-2 px-4 py-2.5 text-xs font-bold text-rose-700 hover:bg-rose-50 rounded-xl transition-colors">
                        <i class="fas fa-file-pdf text-rose-500 text-sm"></i>
                        <span>Reporte PDF / Imprimir</span>
                    </a>
                </div>
            </div>
            @if(auth()->user()->hasRole('recepcionista') || auth()->user()->hasRole('administrador'))
            <a href="{{ route('ventas.create') }}" class="bg-rose-500 hover:bg-rose-600 text-white px-6 py-3 rounded-2xl font-bold shadow-lg shadow-rose-200 transition-all flex items-center gap-2">
                <i class="fas fa-shopping-cart"></i> Registrar Nueva Venta
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
                    <th class="p-4 font-bold border-b border-gray-100 rounded-tl-2xl">ID Venta / Fecha</th>
                    <th class="p-4 font-bold border-b border-gray-100">Cliente</th>
                    <th class="p-4 font-bold border-b border-gray-100">Vendedor</th>
                    <th class="p-4 font-bold border-b border-gray-100">Subtotal</th>
                    <th class="p-4 font-bold border-b border-gray-100">Descuento</th>
                    <th class="p-4 font-bold border-b border-gray-100">Total</th>
                    <th class="p-4 font-bold border-b border-gray-100">Método Pago</th>
                    <th class="p-4 font-bold border-b border-gray-100">Estado Pago</th>
                    <th class="p-4 font-bold border-b border-gray-100 rounded-tr-2xl text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="text-sm">
                @forelse($ventas as $venta)
                <tr class="hover:bg-rose-50/30 transition-colors group">
                    <td class="p-4 border-b border-gray-50">
                        <span class="font-bold text-gray-800">#{{ $venta->id }}</span><br>
                        <span class="text-gray-500 text-xs">{{ $venta->fecha_venta->format('d M, Y h:i A') }}</span>
                    </td>
                    <td class="p-4 border-b border-gray-50 font-bold text-gray-700">
                        {{ $venta->cliente_nombre }}
                        @if($venta->cliente)
                            <span class="block text-[10px] text-rose-500 font-extrabold uppercase">Registrado</span>
                        @else
                            <span class="block text-[10px] text-gray-400 font-extrabold uppercase">Casual</span>
                        @endif
                    </td>
                    <td class="p-4 border-b border-gray-50 text-gray-600 font-medium">
                        {{ $venta->vendedor->name }}
                    </td>
                    <td class="p-4 border-b border-gray-50 text-gray-600 font-semibold">
                        Bs{{ number_format($venta->subtotal, 2) }}
                    </td>
                    <td class="p-4 border-b border-gray-50 text-rose-500 font-bold">
                        -Bs{{ number_format($venta->descuento, 2) }}
                    </td>
                    <td class="p-4 border-b border-gray-50 text-gray-800 font-black">
                        Bs{{ number_format($venta->total, 2) }}
                    </td>
                    <td class="p-4 border-b border-gray-50">
                        <span class="px-2.5 py-1 bg-gray-100 text-gray-700 rounded-lg text-xs font-bold capitalize">
                            {{ $venta->metodo_pago }}
                        </span>
                    </td>
                    <td class="p-4 border-b border-gray-50">
                        @if($venta->estado_pago === 'completado')
                            <span class="px-2.5 py-1 bg-emerald-100 text-emerald-800 border border-emerald-200 rounded-lg text-xs font-bold capitalize">
                                Completado
                            </span>
                        @elseif($venta->estado_pago === 'pendiente')
                            <span class="px-2.5 py-1 bg-amber-100 text-amber-800 border border-amber-200 rounded-lg text-xs font-bold capitalize animate-pulse">
                                Pendiente
                            </span>
                        @else
                            <span class="px-2.5 py-1 bg-rose-100 text-rose-800 border border-rose-200 rounded-lg text-xs font-bold capitalize">
                                {{ $venta->estado_pago }}
                            </span>
                        @endif
                    </td>
                    <td class="p-4 border-b border-gray-50 text-right space-x-2">
                        <a href="{{ route('ventas.show', $venta->id) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-gray-50 text-gray-500 hover:bg-rose-50 hover:text-rose-500 transition-colors" title="Ver Detalle">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('ventas.ticket', $venta->id) }}" target="_blank" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-gray-50 text-gray-500 hover:bg-indigo-50 hover:text-indigo-500 transition-colors" title="Imprimir Ticket">
                            <i class="fas fa-ticket-alt"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="p-8 text-center text-gray-400">
                        No hay ventas registradas en el sistema.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-6">
    {{ $ventas->links() }}
</div>
@endsection
