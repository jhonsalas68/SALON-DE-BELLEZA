@extends('layouts.app')

@section('title', 'Comisiones de Estilistas - Salon Anita')

@section('header')
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-3xl font-extrabold text-gray-800 tracking-tight">Comisiones de Estilistas</h2>
            <p class="text-gray-500 font-medium">Control y pagos de comisiones por servicios realizados.</p>
        </div>
        <details class="relative inline-block text-left group">
            <summary class="bg-stone-800 hover:bg-stone-900 text-white px-5 py-3 rounded-2xl font-bold shadow-md transition-all flex items-center gap-2 text-sm cursor-pointer list-none select-none">
                <i class="fas fa-file-export text-amber-400"></i>
                <span>Exportar</span>
                <i class="fas fa-chevron-down text-xs ml-1 group-open:rotate-180 transition-transform"></i>
            </summary>
            <div class="origin-top-right absolute right-0 mt-2 w-48 rounded-2xl shadow-xl bg-white ring-1 ring-black ring-opacity-5 z-50 divide-y divide-gray-100 p-1">
                <a href="{{ route('reports.export', ['modulo' => 'comisiones', 'format' => 'excel']) }}" class="flex items-center space-x-2 px-4 py-2.5 text-xs font-bold text-emerald-700 hover:bg-emerald-50 rounded-xl transition-colors">
                    <i class="fas fa-file-excel text-emerald-500 text-sm"></i>
                    <span>Exportar Excel (.csv)</span>
                </a>
                <a href="{{ route('reports.export', ['modulo' => 'comisiones', 'format' => 'pdf']) }}" class="flex items-center space-x-2 px-4 py-2.5 text-xs font-bold text-rose-700 hover:bg-rose-50 rounded-xl transition-colors">
                    <i class="fas fa-file-pdf text-rose-500 text-sm"></i>
                    <span>Reporte PDF / Imprimir</span>
                </a>
            </div>
        </details>
    </div>
@endsection

@section('content')
<!-- Panel de Métricas / Tarjetas -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <!-- Tarjeta Total Ganado -->
    <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 p-6 rounded-3xl text-white shadow-lg shadow-indigo-100 flex items-center justify-between">
        <div>
            <p class="text-xs font-black uppercase tracking-wider text-indigo-150">Total Acumulado</p>
            <h3 class="text-3xl font-extrabold mt-1">Bs{{ number_format($totalComisiones, 2) }}</h3>
        </div>
        <div class="w-12 h-12 bg-white/10 rounded-2xl flex items-center justify-center text-xl">
            <i class="fas fa-wallet"></i>
        </div>
    </div>

    <!-- Tarjeta Pendiente -->
    <div class="bg-gradient-to-br from-amber-400 to-amber-500 p-6 rounded-3xl text-white shadow-lg shadow-amber-100 flex items-center justify-between">
        <div>
            <p class="text-xs font-black uppercase tracking-wider text-amber-100">Comisiones Pendientes</p>
            <h3 class="text-3xl font-extrabold mt-1">Bs{{ number_format($comisionesPendientes, 2) }}</h3>
        </div>
        <div class="w-12 h-12 bg-white/10 rounded-2xl flex items-center justify-center text-xl">
            <i class="fas fa-clock"></i>
        </div>
    </div>

    <!-- Tarjeta Pagado -->
    <div class="bg-gradient-to-br from-emerald-400 to-emerald-500 p-6 rounded-3xl text-white shadow-lg shadow-emerald-100 flex items-center justify-between">
        <div>
            <p class="text-xs font-black uppercase tracking-wider text-emerald-150">Comisiones Pagadas</p>
            <h3 class="text-3xl font-extrabold mt-1">Bs{{ number_format($comisionesPagadas, 2) }}</h3>
        </div>
        <div class="w-12 h-12 bg-white/10 rounded-2xl flex items-center justify-center text-xl">
            <i class="fas fa-check-circle"></i>
        </div>
    </div>
</div>

<!-- Filtros de Búsqueda -->
<div class="bg-white p-6 rounded-3xl shadow-sm border border-rose-50 mb-8">
    <form action="{{ route('comisiones.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
        @if(!auth()->user()->hasRole('estilista'))
        <div>
            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Estilista</label>
            <select name="estilista_id" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-1 focus:ring-rose-200 text-gray-700 text-sm font-semibold">
                <option value="">Todos los Estilistas</option>
                @foreach($estilistas as $estilista)
                    <option value="{{ $estilista->id }}" {{ request('estilista_id') == $estilista->id ? 'selected' : '' }}>
                        {{ $estilista->name }}
                    </option>
                @endforeach
            </select>
        </div>
        @endif
        
        <div>
            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Estado de Pago</label>
            <select name="estado" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-1 focus:ring-rose-200 text-gray-700 text-sm font-semibold">
                <option value="">Todos los Estados</option>
                <option value="pendiente" {{ request('estado') === 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                <option value="pagado" {{ request('estado') === 'pagado' ? 'selected' : '' }}>Pagado</option>
            </select>
        </div>

        <div class="flex gap-2">
            <button type="submit" class="flex-1 bg-rose-500 hover:bg-rose-600 text-white font-bold px-6 py-3 rounded-xl transition-all shadow-md text-sm flex items-center justify-center gap-2">
                <i class="fas fa-filter"></i> Filtrar
            </button>
            <a href="{{ route('comisiones.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold px-5 py-3 rounded-xl transition-colors text-sm flex items-center justify-center">
                Limpiar
            </a>
        </div>
    </form>
</div>

<!-- Tabla comisiones -->
<div class="bg-white rounded-3xl shadow-sm border border-rose-50 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                    <th class="p-4 font-bold border-b border-gray-100 rounded-tl-2xl">Fecha</th>
                    @if(!auth()->user()->hasRole('estilista'))
                    <th class="p-4 font-bold border-b border-gray-100">Estilista</th>
                    @endif
                    <th class="p-4 font-bold border-b border-gray-100">Cliente / Servicio</th>
                    <th class="p-4 font-bold border-b border-gray-100">Monto Servicio</th>
                    <th class="p-4 font-bold border-b border-gray-100">Comisión (%)</th>
                    <th class="p-4 font-bold border-b border-gray-100">Comisión Ganada</th>
                    <th class="p-4 font-bold border-b border-gray-100">Estado</th>
                    @if(auth()->user()->hasRole('administrador'))
                    <th class="p-4 font-bold border-b border-gray-100 rounded-tr-2xl text-right">Acciones</th>
                    @endif
                </tr>
            </thead>
            <tbody class="text-sm">
                @forelse($comisiones as $com)
                <tr class="hover:bg-rose-50/30 transition-colors group">
                    <td class="p-4 border-b border-gray-50 text-gray-500">
                        {{ $com->fecha_calculo->format('d M, Y h:i A') }}
                    </td>
                    @if(!auth()->user()->hasRole('estilista'))
                    <td class="p-4 border-b border-gray-50 font-bold text-gray-800">
                        {{ $com->estilista->name }}
                    </td>
                    @endif
                    <td class="p-4 border-b border-gray-50">
                        <span class="font-semibold text-gray-800">{{ $com->cita->servicio->nombre ?? 'Servicio Eliminado' }}</span><br>
                        <span class="text-gray-400 text-xs">Cliente: {{ $com->cita->cliente->name ?? 'Casual' }}</span>
                    </td>
                    <td class="p-4 border-b border-gray-50 text-gray-600 font-semibold">
                        Bs{{ number_format($com->monto_servicio, 2) }}
                    </td>
                    <td class="p-4 border-b border-gray-50 text-gray-500 font-medium">
                        {{ number_format($com->porcentaje_comision, 0) }}%
                    </td>
                    <td class="p-4 border-b border-gray-50 font-black text-gray-800">
                        Bs{{ number_format($com->monto_comision, 2) }}
                    </td>
                    <td class="p-4 border-b border-gray-50">
                        @if($com->estado === 'pendiente')
                            <span class="px-2.5 py-1 bg-yellow-100 text-yellow-700 rounded-lg text-xs font-bold">Pendiente de Pago</span>
                        @else
                            <span class="px-2.5 py-1 bg-emerald-100 text-emerald-700 rounded-lg text-xs font-bold">Pagada</span>
                        @endif
                    </td>
                    @if(auth()->user()->hasRole('administrador'))
                    <td class="p-4 border-b border-gray-50 text-right">
                        @if($com->estado === 'pendiente')
                        <form action="{{ route('comisiones.pagar', $com->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="bg-emerald-500 hover:bg-emerald-600 text-white font-bold px-3 py-1.5 rounded-lg text-xs transition-colors shadow-sm">
                                <i class="fas fa-check"></i> Marcar como Pagado
                            </button>
                        </form>
                        @else
                        <span class="text-emerald-500 font-bold text-xs"><i class="fas fa-check-double"></i> Concluido</span>
                        @endif
                    </td>
                    @endif
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="p-8 text-center text-gray-400">
                        No se encontraron registros de comisiones.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-6">
    {{ $comisiones->links() }}
</div>
@endsection
