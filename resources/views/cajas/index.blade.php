@extends('layouts.app')

@section('title', 'Caja Chica y Arqueo - Salón Anita')

@section('header')
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-black text-gray-900 tracking-tight flex items-center gap-3">
            <i class="fas fa-cash-register text-rose-500"></i>
            Control de Caja Chica y Arqueo Diario
        </h1>
        <p class="text-xs font-semibold text-gray-500 mt-1">
            Gestión de turnos de caja, fondo inicial, gastos operativos y arqueo de cierre.
        </p>
    </div>
    <div class="flex items-center gap-3">
        <div class="relative inline-block text-left" x-data="{ open: false }">
            <button @click="open = !open" type="button" class="bg-stone-800 hover:bg-stone-900 text-white px-5 py-2.5 rounded-xl font-bold shadow-md transition-all flex items-center gap-2 text-xs">
                <i class="fas fa-file-export text-amber-400"></i>
                <span>Exportar</span>
                <i class="fas fa-chevron-down text-xs"></i>
            </button>
            <div x-show="open" x-cloak style="display: none;" @click.away="open = false" class="origin-top-right absolute right-0 mt-2 w-48 rounded-2xl shadow-xl bg-white ring-1 ring-black ring-opacity-5 z-50 divide-y divide-gray-100 p-1">
                <a href="{{ route('reports.export', ['modulo' => 'cajas', 'format' => 'excel']) }}" class="flex items-center space-x-2 px-4 py-2.5 text-xs font-bold text-emerald-700 hover:bg-emerald-50 rounded-xl transition-colors">
                    <i class="fas fa-file-excel text-emerald-500 text-sm"></i>
                    <span>Exportar Excel (.csv)</span>
                </a>
                <a href="{{ route('reports.export', ['modulo' => 'cajas', 'format' => 'pdf']) }}" class="flex items-center space-x-2 px-4 py-2.5 text-xs font-bold text-rose-700 hover:bg-rose-50 rounded-xl transition-colors">
                    <i class="fas fa-file-pdf text-rose-500 text-sm"></i>
                    <span>Reporte PDF / Imprimir</span>
                </a>
            </div>
        </div>
        @if(!$cajaActual)
        <button onclick="openModal('abrirCajaModal')" class="px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs rounded-xl shadow-md transition-all flex items-center space-x-2">
            <i class="fas fa-key"></i>
            <span>Abrir Turno de Caja</span>
        </button>
        @else
        <button onclick="openModal('movimientoModal')" class="px-4 py-2.5 bg-gray-900 hover:bg-black text-white font-bold text-xs rounded-xl shadow transition-all flex items-center space-x-2">
            <i class="fas fa-plus-circle"></i>
            <span>Nuevo Movimiento</span>
        </button>
        <button onclick="openModal('cerrarCajaModal')" class="px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs rounded-xl shadow-md transition-all flex items-center space-x-2">
            <i class="fas fa-lock"></i>
            <span>Cerrar y Arquear Caja</span>
        </button>
        @endif
    </div>
</div>
@endsection

@section('content')
<!-- Tarjeta del Turno Actual Activo -->
@if($cajaActual)
<div class="bg-white rounded-3xl border border-rose-100 shadow-sm p-6 mb-8 relative overflow-hidden">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-gray-100 pb-6 mb-6">
        <div class="flex items-center space-x-4">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-xl shadow-inner">
                <i class="fas fa-door-open"></i>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-lg font-black text-gray-900">Turno de Caja Activo #{{ $cajaActual->id }}</h2>
                    <span class="px-3 py-0.5 bg-emerald-100 text-emerald-800 text-[10px] font-black uppercase tracking-wider rounded-full animate-pulse">Abierta</span>
                </div>
                <p class="text-xs text-gray-500 font-semibold mt-0.5">
                    Abierta por {{ $cajaActual->user->name ?? 'Usuario' }} el {{ $cajaActual->fecha_apertura->format('d/m/Y a las H:i') }}
                </p>
            </div>
        </div>
        <div class="text-right">
            <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Efectivo Esperado en Caja</span>
            <h3 class="text-3xl font-black text-rose-600 tracking-tight">Bs {{ number_format($montoEsperado, 2) }}</h3>
        </div>
    </div>

    <!-- Desglose de Operaciones del Turno -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 text-center">
        <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100">
            <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Monto Inicial</span>
            <p class="text-base font-black text-gray-800 mt-1">Bs {{ number_format($cajaActual->monto_apertura, 2) }}</p>
        </div>
        <div class="p-4 bg-emerald-50/50 rounded-2xl border border-emerald-100">
            <span class="text-[10px] font-black uppercase tracking-widest text-emerald-600">Venta Productos</span>
            <p class="text-base font-black text-emerald-800 mt-1">+ Bs {{ number_format($montoVentasEfectivo, 2) }}</p>
        </div>
        <div class="p-4 bg-rose-50/50 rounded-2xl border border-rose-100">
            <span class="text-[10px] font-black uppercase tracking-widest text-rose-600">Servicios Cobrados</span>
            <p class="text-base font-black text-rose-800 mt-1">+ Bs {{ number_format($montoServiciosEfectivo, 2) }}</p>
        </div>
        <div class="p-4 bg-indigo-50/50 rounded-2xl border border-indigo-100">
            <span class="text-[10px] font-black uppercase tracking-widest text-indigo-600">Ingresos Extra</span>
            <p class="text-base font-black text-indigo-800 mt-1">+ Bs {{ number_format($montoIngresos, 2) }}</p>
        </div>
        <div class="p-4 bg-amber-50/50 rounded-2xl border border-amber-100 col-span-2 md:col-span-1">
            <span class="text-[10px] font-black uppercase tracking-widest text-amber-600">Egresos / Gastos</span>
            <p class="text-base font-black text-amber-800 mt-1">- Bs {{ number_format($montoEgresos, 2) }}</p>
        </div>
    </div>

    <!-- Lista de Movimientos de Caja Chica -->
    <div class="mt-6 border-t border-gray-100 pt-6">
        <h4 class="text-xs font-black text-gray-900 uppercase tracking-wider mb-3">Movimientos Registrados en este Turno</h4>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-[10px] uppercase font-extrabold text-gray-400 border-b border-gray-100">
                        <th class="py-2.5 px-4">Hora</th>
                        <th class="py-2.5 px-4">Tipo</th>
                        <th class="py-2.5 px-4">Concepto</th>
                        <th class="py-2.5 px-4 text-right">Monto</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 font-semibold">
                    @forelse($cajaActual->movimientos as $mov)
                    <tr>
                        <td class="py-3 px-4 text-gray-500">{{ $mov->created_at->format('H:i') }}</td>
                        <td class="py-3 px-4">
                            @if($mov->tipo === 'ingreso')
                            <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 text-[10px] font-black uppercase rounded-full">Ingreso</span>
                            @else
                            <span class="px-2 py-0.5 bg-amber-100 text-amber-800 text-[10px] font-black uppercase rounded-full">Gasto / Egreso</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-gray-800">{{ $mov->concepto }}</td>
                        <td class="py-3 px-4 text-right font-black {{ $mov->tipo === 'ingreso' ? 'text-emerald-600' : 'text-amber-600' }}">
                            {{ $mov->tipo === 'ingreso' ? '+' : '-' }} Bs {{ number_format($mov->monto, 2) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-4 text-center text-gray-400">No se han registrado gastos ni ingresos adicionales en este turno.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@else
<div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-3xl p-8 text-white shadow-xl mb-8 flex flex-col items-center justify-center text-center py-12">
    <div class="w-16 h-16 rounded-3xl bg-white/10 flex items-center justify-center text-rose-400 text-3xl mb-4 shadow-inner">
        <i class="fas fa-cash-register"></i>
    </div>
    <h2 class="text-xl font-black tracking-tight">No Hay Turno de Caja Abierto</h2>
    <p class="text-xs text-gray-400 max-w-md mt-2 font-medium">
        Abre un turno de caja indicando el saldo de efectivo inicial para empezar a registrar ventas y controlar el dinero de la caja chica.
    </p>
    <button onclick="openModal('abrirCajaModal')" class="mt-6 px-6 py-3 bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-xs rounded-2xl shadow-lg shadow-rose-900/50 transition-all flex items-center space-x-2">
        <i class="fas fa-key"></i>
        <span>Abrir Turno de Caja Ahora</span>
    </button>
</div>
@endif

<!-- Tabla de Historial de Turnos de Caja -->
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="p-6 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
        <div>
            <h2 class="text-base font-extrabold text-gray-900 flex items-center gap-2">
                <i class="fas fa-history text-rose-500"></i>
                Historial de Arqueos y Turnos Anteriores
            </h2>
            <p class="text-xs text-gray-500 font-medium">Registro histórico de cierres de caja y diferencias de efectivo</p>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 text-[11px] uppercase tracking-wider font-extrabold text-gray-400 border-b border-gray-100">
                    <th class="py-3 px-6">ID Turno</th>
                    <th class="py-3 px-6">Responsable</th>
                    <th class="py-3 px-6">Apertura</th>
                    <th class="py-3 px-6">Cierre</th>
                    <th class="py-3 px-6 text-right">Inicial</th>
                    <th class="py-3 px-6 text-right">Esperado</th>
                    <th class="py-3 px-6 text-right">Contado</th>
                    <th class="py-3 px-6 text-center">Diferencia</th>
                    <th class="py-3 px-6 text-center">Estado</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-xs font-semibold">
                @forelse($historialCajas as $caja)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="py-4 px-6 font-extrabold text-gray-900">#{{ $caja->id }}</td>
                    <td class="py-4 px-6 font-bold text-gray-800">{{ $caja->user->name ?? 'Usuario' }}</td>
                    <td class="py-4 px-6 text-gray-500">{{ $caja->fecha_apertura->format('d/m/Y H:i') }}</td>
                    <td class="py-4 px-6 text-gray-500">{{ $caja->fecha_cierre ? $caja->fecha_cierre->format('d/m/Y H:i') : '-' }}</td>
                    <td class="py-4 px-6 text-right font-bold">Bs {{ number_format($caja->monto_apertura, 2) }}</td>
                    <td class="py-4 px-6 text-right font-bold text-gray-700">
                        {{ $caja->monto_esperado_efectivo ? 'Bs '.number_format($caja->monto_esperado_efectivo, 2) : '-' }}
                    </td>
                    <td class="py-4 px-6 text-right font-black text-gray-900">
                        {{ $caja->monto_cierre_efectivo ? 'Bs '.number_format($caja->monto_cierre_efectivo, 2) : '-' }}
                    </td>
                    <td class="py-4 px-6 text-center">
                        @if($caja->estado === 'cerrada')
                            @if($caja->diferencia == 0)
                            <span class="px-2.5 py-1 bg-emerald-100 text-emerald-800 text-[10px] font-black rounded-full">Cuadrada (0)</span>
                            @elseif($caja->diferencia > 0)
                            <span class="px-2.5 py-1 bg-indigo-100 text-indigo-800 text-[10px] font-black rounded-full">+Bs {{ number_format($caja->diferencia, 2) }}</span>
                            @else
                            <span class="px-2.5 py-1 bg-rose-100 text-rose-800 text-[10px] font-black rounded-full">-Bs {{ number_format(abs($caja->diferencia), 2) }}</span>
                            @endif
                        @else
                        <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="py-4 px-6 text-center">
                        @if($caja->estado === 'abierta')
                        <span class="px-2.5 py-1 bg-emerald-100 text-emerald-800 text-[10px] font-black uppercase rounded-full">Abierta</span>
                        @else
                        <span class="px-2.5 py-1 bg-gray-100 text-gray-700 text-[10px] font-black uppercase rounded-full">Cerrada</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="py-6 text-center text-gray-400">No hay historial de turnos de caja registrados.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Abrir Caja -->
<div id="abrirCajaModal" class="fixed inset-0 z-50 hidden bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl">
        <h3 class="text-lg font-black text-gray-900 mb-2">Abrir Turno de Caja</h3>
        <p class="text-xs text-gray-500 mb-6 font-semibold">Indica el monto de efectivo inicial en caja chica</p>
        <form action="{{ route('cajas.abrir') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-700 uppercase mb-2">Monto Inicial (Bs)</label>
                <input type="number" step="0.01" name="monto_apertura" value="100.00" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-base font-extrabold text-gray-900 focus:outline-none focus:border-rose-500">
            </div>
            <div class="mb-6">
                <label class="block text-xs font-bold text-gray-700 uppercase mb-2">Observaciones</label>
                <textarea name="observaciones" rows="2" placeholder="Notas iniciales..." class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs font-semibold focus:outline-none focus:border-rose-500"></textarea>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeModal('abrirCajaModal')" class="px-4 py-2.5 text-xs font-bold text-gray-500 hover:text-gray-900">Cancelar</button>
                <button type="submit" class="px-6 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs rounded-xl shadow-md">Abrir Caja</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Registrar Movimiento -->
@if($cajaActual)
<div id="movimientoModal" class="fixed inset-0 z-50 hidden bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl">
        <h3 class="text-lg font-black text-gray-900 mb-2">Nuevo Movimiento de Caja Chica</h3>
        <p class="text-xs text-gray-500 mb-6 font-semibold">Registra un ingreso extra o gasto operativo diario</p>
        <form action="{{ route('cajas.movimiento', $cajaActual->id) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-700 uppercase mb-2">Tipo de Movimiento</label>
                <select name="tipo" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs font-bold text-gray-800">
                    <option value="egreso">Gasto / Egreso (Salida de dinero)</option>
                    <option value="ingreso">Ingreso Extra (Entrada de dinero)</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-700 uppercase mb-2">Monto (Bs)</label>
                <input type="number" step="0.01" name="monto" required placeholder="0.00" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-bold text-gray-900">
            </div>
            <div class="mb-6">
                <label class="block text-xs font-bold text-gray-700 uppercase mb-2">Concepto / Motivo</label>
                <input type="text" name="concepto" required placeholder="Ej: Compra de insumo de limpieza, café, etc." class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs font-semibold">
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeModal('movimientoModal')" class="px-4 py-2.5 text-xs font-bold text-gray-500 hover:text-gray-900">Cancelar</button>
                <button type="submit" class="px-6 py-2.5 bg-gray-900 hover:bg-black text-white font-bold text-xs rounded-xl shadow-md">Guardar Movimiento</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Cerrar Caja -->
<div id="cerrarCajaModal" class="fixed inset-0 z-50 hidden bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl">
        <h3 class="text-lg font-black text-gray-900 mb-2">Cerrar y Arquear Caja</h3>
        <p class="text-xs text-gray-500 mb-4 font-semibold">Ingresa el total de dinero físico contado en caja</p>
        
        <div class="p-4 bg-rose-50 rounded-2xl border border-rose-100 mb-4 text-center">
            <span class="text-[10px] font-black uppercase text-rose-500">Monto Esperado en Sistema</span>
            <h4 class="text-2xl font-black text-rose-700">Bs {{ number_format($montoEsperado, 2) }}</h4>
        </div>

        <form action="{{ route('cajas.cerrar', $cajaActual->id) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-700 uppercase mb-2">Efectivo Real Contado en Físico (Bs)</label>
                <input type="number" step="0.01" name="monto_cierre_efectivo" required value="{{ $montoEsperado }}" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-base font-extrabold text-gray-900 focus:outline-none focus:border-rose-500">
            </div>
            <div class="mb-6">
                <label class="block text-xs font-bold text-gray-700 uppercase mb-2">Observaciones de Cierre</label>
                <textarea name="observaciones" rows="2" placeholder="Cierre normal, sin novedades..." class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs font-semibold focus:outline-none focus:border-rose-500"></textarea>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeModal('cerrarCajaModal')" class="px-4 py-2.5 text-xs font-bold text-gray-500 hover:text-gray-900">Cancelar</button>
                <button type="submit" class="px-6 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs rounded-xl shadow-md">Confirmar Cierre y Arqueo</button>
            </div>
        </form>
    </div>
</div>
@endif

<script>
    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
    }
    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
    }
</script>
@endsection
