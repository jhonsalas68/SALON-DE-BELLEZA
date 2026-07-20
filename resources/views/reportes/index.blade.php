@extends('layouts.app')

@section('title', 'Reportes Administrativos - Salón Anita')

@section('header')
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-black text-gray-900 tracking-tight flex items-center gap-3">
            <i class="fas fa-chart-line text-rose-500"></i>
            Reportes Administrativos
        </h1>
        <p class="text-xs font-semibold text-gray-500 mt-1">
            Análisis ejecutivo de ventas, servicios completados, comisiones e inventario (CU20)
        </p>
    </div>
    <div class="flex items-center space-x-3">
        <a href="{{ route('reportes.imprimir', request()->all()) }}" target="_blank" class="px-5 py-2.5 bg-gray-900 hover:bg-black text-white text-xs font-bold rounded-xl shadow-md transition-all flex items-center space-x-2">
            <i class="fas fa-print"></i>
            <span>Imprimir Reporte</span>
        </a>
    </div>
</div>
@endsection

@section('content')
<!-- Filtro por Rango de Fechas -->
<div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm mb-8">
    <form action="{{ route('reportes.index') }}" method="GET" class="flex flex-wrap items-end gap-4">
        <div>
            <label class="block text-xs font-bold text-gray-700 uppercase mb-2">Periodo de Reporte</label>
            <select name="rango" id="rangoSelect" onchange="toggleCustomDates()" class="px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-bold text-gray-800 focus:outline-none focus:border-rose-500">
                <option value="mes" {{ $rango === 'mes' ? 'selected' : '' }}>Este Mes ({{ now()->translatedFormat('F Y') }})</option>
                <option value="semana" {{ $rango === 'semana' ? 'selected' : '' }}>Esta Semana</option>
                <option value="hoy" {{ $rango === 'hoy' ? 'selected' : '' }}>Hoy ({{ now()->format('d/m/Y') }})</option>
                <option value="personalizado" {{ $rango === 'personalizado' ? 'selected' : '' }}>Rango Personalizado</option>
            </select>
        </div>

        <div id="customDateFields" class="flex items-center gap-3 {{ $rango === 'personalizado' ? '' : 'hidden' }}">
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-2">Desde</label>
                <input type="date" name="fecha_inicio" value="{{ request('fecha_inicio', $fechaInicio->format('Y-m-d')) }}" class="px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm font-semibold text-gray-800 focus:outline-none focus:border-rose-500">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-2">Hasta</label>
                <input type="date" name="fecha_fin" value="{{ request('fecha_fin', $fechaFin->format('Y-m-d')) }}" class="px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm font-semibold text-gray-800 focus:outline-none focus:border-rose-500">
            </div>
        </div>

        <button type="submit" class="px-6 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-bold text-sm rounded-xl shadow-md transition-all flex items-center space-x-2">
            <i class="fas fa-filter"></i>
            <span>Filtrar</span>
        </button>
    </form>
</div>

<!-- Tarjetas de Métricas KPI -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total Ingresos Combinados -->
    <div class="bg-gradient-to-br from-gray-900 to-gray-800 p-6 rounded-2xl text-white shadow-lg relative overflow-hidden">
        <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-extrabold uppercase tracking-widest text-gray-400">Total Ingresos</span>
            <div class="w-10 h-10 bg-white/10 rounded-xl flex items-center justify-center text-rose-400">
                <i class="fas fa-wallet text-lg"></i>
            </div>
        </div>
        <h3 class="text-3xl font-black text-white tracking-tight">Bs {{ number_format($totalIngresoVentas + $totalIngresoServicios, 2) }}</h3>
        <p class="text-xs text-gray-400 mt-2 font-medium">Servicios + Ventas en el periodo seleccionado</p>
    </div>

    <!-- Ingresos por Ventas de Productos -->
    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm relative">
        <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-extrabold uppercase tracking-widest text-gray-500">Ventas Productos</span>
            <div class="w-10 h-10 bg-emerald-50 rounded-xl flex items-center justify-center text-emerald-600">
                <i class="fas fa-cash-register text-lg"></i>
            </div>
        </div>
        <h3 class="text-3xl font-black text-gray-900 tracking-tight">Bs {{ number_format($totalIngresoVentas, 2) }}</h3>
        <p class="text-xs text-gray-500 mt-2 font-medium">{{ $totalCantidadVentas }} ventas de productos concretadas</p>
    </div>

    <!-- Ingresos por Servicios -->
    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm relative">
        <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-extrabold uppercase tracking-widest text-gray-500">Servicios Realizados</span>
            <div class="w-10 h-10 bg-rose-50 rounded-xl flex items-center justify-center text-rose-600">
                <i class="fas fa-cut text-lg"></i>
            </div>
        </div>
        <h3 class="text-3xl font-black text-gray-900 tracking-tight">Bs {{ number_format($totalIngresoServicios, 2) }}</h3>
        <p class="text-xs text-gray-500 mt-2 font-medium">{{ $totalServiciosCompletados }} citas de servicios completadas</p>
    </div>

    <!-- Total Comisiones -->
    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm relative">
        <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-extrabold uppercase tracking-widest text-gray-500">Comisiones Estilistas</span>
            <div class="w-10 h-10 bg-indigo-50 rounded-xl flex items-center justify-center text-indigo-600">
                <i class="fas fa-hand-holding-usd text-lg"></i>
            </div>
        </div>
        <h3 class="text-3xl font-black text-gray-900 tracking-tight">Bs {{ number_format($totalComisionesGeneradas, 2) }}</h3>
        <div class="flex items-center gap-3 text-xs mt-2 font-bold">
            <span class="text-amber-600">Pendiente: Bs {{ number_format($totalComisionesPendientes, 2) }}</span>
            <span class="text-emerald-600">Pagado: Bs {{ number_format($totalComisionesPagadas, 2) }}</span>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <!-- Tabla: Rendimiento por Estilista -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden flex flex-col">
        <div class="p-6 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
            <div>
                <h2 class="text-base font-extrabold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-user-check text-rose-500"></i>
                    Rendimiento por Estilista (CU8 & CU18)
                </h2>
                <p class="text-xs text-gray-500 font-medium">Servicios atendidos y comisiones calculadas</p>
            </div>
        </div>
        <div class="overflow-x-auto flex-1">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-[11px] uppercase tracking-wider font-extrabold text-gray-400 border-b border-gray-100">
                        <th class="py-3 px-6">Estilista</th>
                        <th class="py-3 px-6 text-center">Citas Completadas</th>
                        <th class="py-3 px-6 text-right">Ingresos Generados</th>
                        <th class="py-3 px-6 text-right">Comisiones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-xs font-semibold">
                    @forelse($rendimientoEstilistas as $estilista)
                    <tr class="hover:bg-rose-50/30 transition-colors">
                        <td class="py-4 px-6 font-extrabold text-gray-900 flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center font-bold text-xs">
                                {{ strtoupper(substr($estilista->name, 0, 1)) }}
                            </div>
                            <span>{{ $estilista->name }}</span>
                        </td>
                        <td class="py-4 px-6 text-center">
                            <span class="px-2.5 py-1 bg-gray-100 text-gray-800 rounded-lg text-xs font-black">
                                {{ $estilista->citas_count }}
                            </span>
                        </td>
                        <td class="py-4 px-6 text-right text-gray-900 font-bold">
                            Bs {{ number_format($estilista->total_ingresos_generados ?: 0, 2) }}
                        </td>
                        <td class="py-4 px-6 text-right text-rose-600 font-black">
                            Bs {{ number_format($estilista->total_comisiones ?: 0, 2) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-6 text-center text-gray-400">No hay registros de estilistas en este periodo.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Tabla: Top Productos Más Vendidos -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden flex flex-col">
        <div class="p-6 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
            <div>
                <h2 class="text-base font-extrabold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-star text-amber-500"></i>
                    Productos Más Vendidos (CU22)
                </h2>
                <p class="text-xs text-gray-500 font-medium">Top productos por unidades vendidas</p>
            </div>
        </div>
        <div class="overflow-x-auto flex-1">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-[11px] uppercase tracking-wider font-extrabold text-gray-400 border-b border-gray-100">
                        <th class="py-3 px-6">Producto</th>
                        <th class="py-3 px-6 text-center">Unidades</th>
                        <th class="py-3 px-6 text-right">Total Ingreso</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-xs font-semibold">
                    @forelse($topProductos as $item)
                    <tr class="hover:bg-amber-50/30 transition-colors">
                        <td class="py-4 px-6 font-extrabold text-gray-900">
                            {{ $item->producto->nombre ?? 'Producto Eliminado' }}
                        </td>
                        <td class="py-4 px-6 text-center">
                            <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 rounded-lg text-xs font-black">
                                {{ $item->total_vendido }} un.
                            </span>
                        </td>
                        <td class="py-4 px-6 text-right text-gray-900 font-bold">
                            Bs {{ number_format($item->total_ingreso, 2) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="py-6 text-center text-gray-400">No hay ventas registradas en este periodo.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Sección: Alertas de Stock Bajo (CU15) -->
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="p-6 border-b border-gray-100 bg-rose-50/30 flex items-center justify-between">
        <div>
            <h2 class="text-base font-extrabold text-gray-900 flex items-center gap-2">
                <i class="fas fa-exclamation-triangle text-rose-600"></i>
                Estado Crítico de Inventario (CU15: Alertas de Stock Bajo)
            </h2>
            <p class="text-xs text-gray-500 font-medium">Productos que alcanzaron o sobrepasaron su nivel mínimo</p>
        </div>
        <a href="{{ route('alertas.index') }}" class="text-xs font-bold text-rose-600 hover:text-rose-700 flex items-center gap-1">
            <span>Ver Centro de Alertas</span>
            <i class="fas fa-arrow-right"></i>
        </a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 text-[11px] uppercase tracking-wider font-extrabold text-gray-400 border-b border-gray-100">
                    <th class="py-3 px-6">Producto</th>
                    <th class="py-3 px-6 text-center">Stock Actual</th>
                    <th class="py-3 px-6 text-center">Stock Mínimo</th>
                    <th class="py-3 px-6 text-center">Estado</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-xs font-semibold">
                @forelse($productosStockBajo as $producto)
                <tr class="hover:bg-rose-50/20 transition-colors">
                    <td class="py-4 px-6 font-extrabold text-gray-900">
                        {{ $producto->nombre }}
                    </td>
                    <td class="py-4 px-6 text-center font-black text-rose-600">
                        {{ $producto->stock }}
                    </td>
                    <td class="py-4 px-6 text-center font-bold text-gray-500">
                        {{ $producto->stock_minimo }}
                    </td>
                    <td class="py-4 px-6 text-center">
                        <span class="px-2.5 py-1 bg-rose-100 text-rose-700 rounded-full text-[10px] font-black uppercase tracking-wider">
                            Stock Bajo
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="py-6 text-center text-emerald-600 font-bold">
                        <i class="fas fa-check-circle mr-1"></i> Todos los productos cuentan con niveles de stock óptimos.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function toggleCustomDates() {
        const select = document.getElementById('rangoSelect');
        const customFields = document.getElementById('customDateFields');
        if (select.value === 'personalizado') {
            customFields.classList.remove('hidden');
        } else {
            customFields.classList.add('hidden');
        }
    }
</script>
@endsection
