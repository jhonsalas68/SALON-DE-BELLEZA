@extends('layouts.app')

@section('title', 'Horarios - Salon Anita')

@section('header')
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-3xl font-extrabold text-gray-800 tracking-tight">Horarios del Personal</h2>
            <p class="text-gray-500 font-medium">Consulta y administra los horarios de trabajo.</p>
        </div>
        @if(auth()->user()->hasPermission('manage_schedules'))
        <a href="{{ route('horarios.create') }}" class="bg-rose-500 hover:bg-rose-600 text-white px-6 py-3 rounded-2xl font-bold transition-all shadow-lg shadow-rose-100 flex items-center justify-center space-x-2">
            <i class="fas fa-plus text-xs"></i>
            <span>Nuevo Horario</span>
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
                    <th class="p-5 text-xs font-black text-rose-400 uppercase tracking-widest border-b border-rose-50">Personal</th>
                    <th class="p-5 text-xs font-black text-rose-400 uppercase tracking-widest border-b border-rose-50">Día</th>
                    <th class="p-5 text-xs font-black text-rose-400 uppercase tracking-widest border-b border-rose-50">Horario</th>
                    <th class="p-5 text-xs font-black text-rose-400 uppercase tracking-widest border-b border-rose-50">Estado</th>
                    @if(auth()->user()->hasPermission('manage_schedules'))
                    <th class="p-5 text-xs font-black text-rose-400 uppercase tracking-widest border-b border-rose-50 text-right">Acciones</th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-rose-50">
                @forelse($horarios as $horario)
                <tr class="hover:bg-rose-50/10 transition-colors">
                    <td class="p-5">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center text-orange-600">
                                <i class="fas fa-user"></i>
                            </div>
                            <div>
                                <p class="font-bold text-gray-800 leading-tight">{{ $horario->user->name ?? 'Usuario Eliminado' }}</p>
                                <p class="text-[10px] text-gray-400 mt-0.5">{{ $horario->user->role->name ?? '' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="p-5 font-bold text-gray-700">
                        {{ ucfirst($horario->dia_semana) }}
                    </td>
                    <td class="p-5 font-medium text-gray-600">
                        {{ \Carbon\Carbon::parse($horario->hora_inicio)->format('H:i') }} - {{ \Carbon\Carbon::parse($horario->hora_fin)->format('H:i') }}
                    </td>
                    <td class="p-5">
                        @if($horario->activo)
                            <span class="px-3 py-1 bg-emerald-100 text-emerald-600 rounded-full text-[10px] font-black uppercase">Activo</span>
                        @else
                            <span class="px-3 py-1 bg-rose-100 text-rose-600 rounded-full text-[10px] font-black uppercase">Inactivo</span>
                        @endif
                    </td>
                    @if(auth()->user()->hasPermission('manage_schedules'))
                    <td class="p-5 text-right">
                        <div class="flex justify-end space-x-2">
                            <a href="{{ route('horarios.edit', $horario) }}" class="p-2 text-blue-500 hover:bg-blue-50 rounded-lg transition-colors" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('horarios.destroy', $horario) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este horario?')">
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
                        <i class="fas fa-calendar-alt text-4xl mb-3 block"></i>
                        <p class="font-medium">No hay horarios registrados.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
