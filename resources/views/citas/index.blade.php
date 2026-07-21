@extends('layouts.app')

@section('title', 'Citas - Salon Anita')

@section('header')
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-3xl font-extrabold text-gray-800 tracking-tight">Gestión de Citas</h2>
            <p class="text-gray-500 font-medium">Administra las reservas, asignaciones de estilistas y finalización de servicios.</p>
        </div>
        @if(auth()->user()->hasRole('recepcionista') || auth()->user()->hasRole('administrador'))
        <a href="{{ route('citas.create') }}" class="bg-rose-500 hover:bg-rose-600 text-white px-6 py-3 rounded-2xl font-bold shadow-lg shadow-rose-200 transition-all flex items-center gap-2">
            <i class="fas fa-plus"></i> Agendar Nueva Cita
        </a>
        @endif
    </div>
@endsection

@section('content')
<div class="bg-white rounded-3xl shadow-sm border border-rose-50 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[800px]">
            <thead>
                <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-100">
                    <th class="p-4 font-bold rounded-tl-2xl">Fecha y Hora</th>
                    <th class="p-4 font-bold">Cliente</th>
                    <th class="p-4 font-bold">Servicio solicitado</th>
                    <th class="p-4 font-bold">Estilista asignado</th>
                    <th class="p-4 font-bold">Estado</th>
                    <th class="p-4 font-bold rounded-tr-2xl text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="text-sm font-semibold divide-y divide-gray-50">
                @forelse($citas as $cita)
                <tr class="hover:bg-rose-50/20 transition-colors group">
                    <td class="p-4">
                        <span class="font-bold text-gray-850">{{ \Carbon\Carbon::parse($cita->fecha)->format('d M, Y') }}</span><br>
                        <span class="text-gray-400 text-xs">{{ \Carbon\Carbon::parse($cita->hora)->format('h:i A') }}</span>
                    </td>
                    <td class="p-4 text-gray-800">
                        {{ $cita->cliente->name ?? 'N/A' }}
                    </td>
                    <td class="p-4">
                        <div class="text-gray-700 font-bold">{{ $cita->servicio->nombre ?? 'N/A' }}</div>
                        <div class="text-xs text-rose-500">Bs{{ number_format($cita->servicio->precio ?? 0, 2) }}</div>
                    </td>
                    <td class="p-4">
                        @if($cita->estilista_id)
                            <div class="flex items-center space-x-2">
                                <div class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center text-xs">
                                    <i class="fas fa-user-circle"></i>
                                </div>
                                <span class="text-gray-800">{{ $cita->estilista->name }}</span>
                            </div>
                        @else
                            @if(auth()->user()->hasRole('recepcionista') || auth()->user()->hasRole('administrador'))
                            <!-- CU12 - Asignar Estilista Rápido -->
                            <form action="{{ route('citas.asignar-estilista', $cita->id) }}" method="POST" class="flex items-center gap-1.5 max-w-[200px]">
                                @csrf
                                <select name="estilista_id" required class="w-full bg-gray-50 border border-gray-200 text-xs rounded-xl px-2 py-2 text-gray-700 font-semibold focus:ring-1 focus:ring-rose-200 focus:border-rose-400 transition-all">
                                    <option value="">-- Asignar --</option>
                                    @foreach($estilistas as $estilista)
                                        <option value="{{ $estilista->id }}">{{ $estilista->name }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="w-8 h-8 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl flex items-center justify-center text-xs shrink-0 transition-colors shadow-sm" title="Confirmar Asignación">
                                    <i class="fas fa-check"></i>
                                </button>
                            </form>
                            @else
                            <span class="text-gray-400 italic text-xs">Sin Asignar</span>
                            @endif
                        @endif
                    </td>
                    <td class="p-4">
                        @if($cita->estado == 'pendiente')
                            <span class="px-2.5 py-1 bg-yellow-100 text-yellow-700 rounded-lg text-xs font-bold">Pendiente</span>
                        @elseif($cita->estado == 'confirmada')
                            <span class="px-2.5 py-1 bg-blue-100 text-blue-700 rounded-lg text-xs font-bold">Confirmada</span>
                        @elseif($cita->estado == 'completada')
                            <span class="px-2.5 py-1 bg-emerald-100 text-emerald-700 rounded-lg text-xs font-bold">Completada</span>
                        @else
                            <span class="px-2.5 py-1 bg-red-105 text-red-700 rounded-lg text-xs font-bold">Cancelada</span>
                        @endif
                    </td>
                    <td class="p-4 text-right">
                        <div class="inline-flex items-center gap-2">
                            <!-- Acciones según estado (CU18, CU19) -->
                            @if($cita->estado !== 'completada' && $cita->estado !== 'cancelada')
                                @if($cita->estilista_id)
                                <form action="{{ route('citas.completar', $cita->id) }}" method="POST" class="inline" onsubmit="return confirm('¿Registrar este servicio como realizado y generar comisión?');">
                                    @csrf
                                    <button type="submit" class="bg-emerald-500 hover:bg-emerald-600 text-white font-bold px-3 py-2 rounded-xl text-xs transition-colors shadow-sm inline-flex items-center gap-1">
                                        <i class="fas fa-check-double"></i> Completar
                                    </button>
                                </form>
                                @else
                                <span class="text-[10px] text-gray-450 italic font-medium max-w-[120px] block leading-tight text-right">Asigne estilista para completar</span>
                                @endif
                            @elseif($cita->estado === 'completada')
                            <a href="{{ route('valoraciones.index') }}" class="bg-amber-500 hover:bg-amber-600 text-white font-bold px-3 py-2 rounded-xl text-xs transition-colors shadow-sm inline-flex items-center gap-1" title="Dejar opinión o valoración del servicio">
                                <i class="fas fa-star"></i> Valorar
                            </a>
                            <a href="{{ route('citas.show-ticket', $cita->id) }}" class="bg-rose-500 hover:bg-rose-600 text-white font-bold px-3 py-2 rounded-xl text-xs transition-colors shadow-sm inline-flex items-center gap-1">
                                <i class="fas fa-print"></i> Ticket
                            </a>
                            @endif

                            <!-- Edición y Borrado -->
                            <a href="{{ route('citas.edit', $cita->id) }}" class="w-8 h-8 rounded-xl bg-gray-50 text-gray-500 hover:bg-amber-50 hover:text-amber-500 flex items-center justify-center transition-colors" title="Editar cita completa">
                                <i class="fas fa-edit text-xs"></i>
                            </a>
                            @if(auth()->user()->hasRole('recepcionista') || auth()->user()->hasRole('administrador'))
                            <form action="{{ route('citas.destroy', $cita->id) }}" method="POST" class="inline" onsubmit="return confirm('¿Eliminar cita por completo?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-8 h-8 rounded-xl bg-gray-50 text-gray-500 hover:bg-red-50 hover:text-red-500 flex items-center justify-center transition-colors">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="p-8 text-center text-gray-400">
                        No hay citas registradas.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-6">
    {{ $citas->links() }}
</div>
@endsection
