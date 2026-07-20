@extends('layouts.app')

@section('title', 'Horarios - Salon Anita')

@section('header')
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-3xl font-extrabold text-gray-800 tracking-tight">Horarios del Personal</h2>
            <p class="text-gray-500 font-medium">Visualiza y administra los turnos semanales de estilistas y recepcionistas.</p>
        </div>
        @if(auth()->user()->hasPermission('manage_schedules'))
        <a href="{{ route('horarios.create') }}" class="bg-rose-500 hover:bg-rose-600 text-white px-6 py-3 rounded-2xl font-bold transition-all shadow-lg shadow-rose-200 flex items-center justify-center space-x-2">
            <i class="fas fa-plus text-xs"></i>
            <span>Nuevo Horario</span>
        </a>
        @endif
    </div>
@endsection

@section('content')
@php
    $dias = [
        'lunes' => 'Lunes',
        'martes' => 'Martes',
        'miercoles' => 'Miércoles',
        'jueves' => 'Jueves',
        'viernes' => 'Viernes',
        'sabado' => 'Sábado',
        'domingo' => 'Domingo'
    ];
@endphp

<!-- Tabs para alternar vistas -->
<div class="mb-6 border-b border-gray-200">
    <nav class="-mb-px flex space-x-6" aria-label="Tabs">
        <button onclick="switchTab('calendar-view', 'list-view', this)" class="tab-btn border-rose-500 text-rose-600 whitespace-nowrap py-4 px-1 border-b-2 font-bold text-sm flex items-center gap-2">
            <i class="fas fa-calendar-alt"></i> Vista Calendario Semanal
        </button>
        <button onclick="switchTab('list-view', 'calendar-view', this)" class="tab-btn border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-semibold text-sm flex items-center gap-2">
            <i class="fas fa-list"></i> Lista Detallada
        </button>
    </nav>
</div>

<!-- VISTA 1: CALENDARIO SEMANAL -->
<div id="calendar-view" class="tab-content bg-white rounded-3xl shadow-sm border border-rose-50 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[900px]">
            <thead>
                <tr class="bg-gray-50 text-gray-500 text-[10px] uppercase tracking-wider border-b border-gray-100">
                    <th class="p-4 font-bold rounded-tl-2xl">Colaborador</th>
                    @foreach($dias as $key => $name)
                    <th class="p-4 font-bold text-center">{{ $name }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="text-sm font-semibold divide-y divide-gray-50">
                @php
                    $horariosAgrupados = $horarios->groupBy('user_id');
                @endphp
                
                @forelse($horariosAgrupados as $userId => $userHorarios)
                    @php $user = $userHorarios->first()->user; @endphp
                    @if($user)
                    <tr class="hover:bg-rose-50/10 transition-colors">
                        <td class="p-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-600 flex items-center justify-center text-white text-xs font-bold shadow-sm">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>
                                <div>
                                    <p class="font-extrabold text-gray-800 leading-tight">{{ $user->name }}</p>
                                    <p class="text-[10px] text-rose-500 font-bold uppercase tracking-wider mt-0.5">{{ $user->role->name ?? '' }}</p>
                                </div>
                            </div>
                        </td>
                        
                        @foreach($dias as $diaKey => $diaName)
                            @php 
                                $horarioDelDia = $userHorarios->where('dia_semana', $diaKey)->first(); 
                            @endphp
                                    <td class="p-4 text-center">
                                        @if($horarioDelDia)
                                            <div class="px-3 py-2.5 rounded-2xl {{ $horarioDelDia->activo ? 'bg-emerald-50 text-emerald-800 border border-emerald-100 shadow-sm' : 'bg-gray-100 text-gray-500' }} text-xs inline-block min-w-[120px]">
                                                <span class="block font-black text-xs tracking-tight">
                                                    {{ \Carbon\Carbon::parse($horarioDelDia->hora_inicio)->format('H:i') }} - {{ \Carbon\Carbon::parse($horarioDelDia->hora_fin)->format('H:i') }}
                                                </span>
                                                @if(!$horarioDelDia->activo)
                                                <span class="text-[8px] uppercase tracking-wider font-extrabold block text-gray-400">Inactivo</span>
                                                @endif
                                                
                                                @if(auth()->user()->hasPermission('manage_schedules'))
                                                <div class="flex items-center justify-center gap-1.5 mt-2 pt-1.5 border-t border-emerald-200/40">
                                                    <a href="{{ route('horarios.edit', $horarioDelDia->id) }}" class="px-2 py-1 bg-blue-500 hover:bg-blue-600 text-white rounded-lg text-[10px] font-bold transition-all shadow-sm flex items-center gap-1" title="Editar este horario">
                                                        <i class="fas fa-edit"></i>
                                                        <span>Editar</span>
                                                    </a>
                                                    <form action="{{ route('horarios.destroy', $horarioDelDia->id) }}" method="POST" class="inline" onsubmit="return confirm('¿Eliminar horario de este día?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="px-2 py-1 bg-rose-500 hover:bg-rose-600 text-white rounded-lg text-[10px] font-bold transition-all shadow-sm flex items-center gap-1" title="Eliminar este horario">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-xs text-gray-400 font-bold bg-gray-50 border border-gray-100 px-3 py-1.5 rounded-xl">Libre</span>
                                        @endif
                                    </td>
                        @endforeach
                    </tr>
                    @endif
                @empty
                    <tr>
                        <td colspan="8" class="p-10 text-center text-gray-400">
                            <i class="fas fa-calendar-alt text-4xl mb-3 block text-gray-250"></i>
                            <p class="font-bold">No hay horarios registrados para generar el calendario.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- VISTA 2: LISTA DETALLADA -->
<div id="list-view" class="tab-content hidden bg-white rounded-3xl shadow-sm border border-rose-50 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 text-gray-500 text-[10px] uppercase tracking-wider border-b border-gray-100">
                    <th class="p-4 font-bold rounded-tl-2xl">Colaborador</th>
                    <th class="p-4 font-bold">Día de la Semana</th>
                    <th class="p-4 font-bold">Rango de Horas</th>
                    <th class="p-4 font-bold">Estado</th>
                    @if(auth()->user()->hasPermission('manage_schedules'))
                    <th class="p-4 font-bold rounded-tr-2xl text-right">Acciones</th>
                    @endif
                </tr>
            </thead>
            <tbody class="text-sm font-semibold divide-y divide-gray-50">
                @forelse($horarios as $horario)
                <tr class="hover:bg-rose-50/10 transition-colors">
                    <td class="p-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-600 flex items-center justify-center text-white text-xs font-bold shadow-sm">
                                {{ strtoupper(substr($horario->user->name ?? '?', 0, 2)) }}
                            </div>
                            <div>
                                <p class="font-extrabold text-gray-800 leading-tight">{{ $horario->user->name ?? 'Usuario Eliminado' }}</p>
                                <p class="text-[10px] text-gray-400 font-bold uppercase mt-0.5">{{ $horario->user->role->name ?? '' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="p-4 text-gray-700 capitalize">
                        {{ $horario->dia_semana }}
                    </td>
                    <td class="p-4 text-gray-600">
                        {{ \Carbon\Carbon::parse($horario->hora_inicio)->format('h:i A') }} - {{ \Carbon\Carbon::parse($horario->hora_fin)->format('h:i A') }}
                    </td>
                    <td class="p-4">
                        @if($horario->activo)
                            <span class="px-2.5 py-1 bg-emerald-100 text-emerald-700 rounded-lg text-xs font-bold">Activo</span>
                        @else
                            <span class="px-2.5 py-1 bg-gray-150 text-gray-500 rounded-lg text-xs font-medium">Inactivo</span>
                        @endif
                    </td>
                    @if(auth()->user()->hasPermission('manage_schedules'))
                    <td class="p-4 text-right">
                        <div class="inline-flex gap-2">
                            <a href="{{ route('horarios.edit', $horario->id) }}" class="w-8 h-8 rounded-lg bg-gray-50 hover:bg-amber-50 text-gray-400 hover:text-amber-500 flex items-center justify-center transition-colors" title="Editar">
                                <i class="fas fa-edit text-xs"></i>
                            </a>
                            <form action="{{ route('horarios.destroy', $horario->id) }}" method="POST" class="inline" onsubmit="return confirm('¿Eliminar este horario?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-8 h-8 rounded-lg bg-gray-50 hover:bg-red-50 text-gray-400 hover:text-red-500 flex items-center justify-center transition-colors" title="Eliminar">
                                    <i class="fas fa-trash-alt text-xs"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                    @endif
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="p-10 text-center text-gray-400">
                        <i class="fas fa-calendar-alt text-4xl mb-3 block"></i>
                        <p class="font-bold">No hay horarios registrados.</p>
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
    function switchTab(activeId, inactiveId, btn) {
        document.getElementById(activeId).classList.remove('hidden');
        document.getElementById(inactiveId).classList.add('hidden');
        
        // Update tab button styles
        document.querySelectorAll('.tab-btn').forEach(button => {
            button.classList.remove('border-rose-500', 'text-rose-600');
            button.classList.add('border-transparent', 'text-gray-500');
            button.classList.replace('font-bold', 'font-semibold');
        });
        
        btn.classList.add('border-rose-500', 'text-rose-600');
        btn.classList.remove('border-transparent', 'text-gray-500');
        btn.classList.replace('font-semibold', 'font-bold');
    }
</script>
@endsection
