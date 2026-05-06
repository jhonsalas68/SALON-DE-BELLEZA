@extends('layouts.app')

@section('title', 'Editar Horario - Salon Anita')

@section('header')
    <div class="flex items-center space-x-4">
        <a href="{{ route('horarios.index') }}" class="text-gray-400 hover:text-rose-500 transition-colors">
            <i class="fas fa-arrow-left text-xl"></i>
        </a>
        <div>
            <h2 class="text-3xl font-extrabold text-gray-800 tracking-tight">Editar Horario</h2>
            <p class="text-gray-500 font-medium">Modificar el turno asignado al empleado.</p>
        </div>
    </div>
@endsection

@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-3xl shadow-sm border border-rose-50 p-8">
    <form action="{{ route('horarios.update', $horario) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')
        
        <div class="space-y-2">
            <label class="text-sm font-bold text-gray-700 uppercase tracking-wide">Personal</label>
            <select name="user_id" class="w-full bg-gray-50 border border-gray-200 text-gray-800 rounded-xl px-4 py-3 focus:ring-2 focus:ring-rose-200 focus:border-rose-400 transition-all" required>
                <option value="">Seleccione un empleado...</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ $horario->user_id == $user->id ? 'selected' : '' }}>{{ $user->name }} ({{ $user->role->name ?? 'Sin Rol' }})</option>
                @endforeach
            </select>
        </div>

        <div class="space-y-2">
            <label class="text-sm font-bold text-gray-700 uppercase tracking-wide">Día de la semana</label>
            <select name="dia_semana" class="w-full bg-gray-50 border border-gray-200 text-gray-800 rounded-xl px-4 py-3 focus:ring-2 focus:ring-rose-200 focus:border-rose-400 transition-all" required>
                <option value="">Seleccione un día...</option>
                <option value="lunes" {{ $horario->dia_semana == 'lunes' ? 'selected' : '' }}>Lunes</option>
                <option value="martes" {{ $horario->dia_semana == 'martes' ? 'selected' : '' }}>Martes</option>
                <option value="miercoles" {{ $horario->dia_semana == 'miercoles' ? 'selected' : '' }}>Miércoles</option>
                <option value="jueves" {{ $horario->dia_semana == 'jueves' ? 'selected' : '' }}>Jueves</option>
                <option value="viernes" {{ $horario->dia_semana == 'viernes' ? 'selected' : '' }}>Viernes</option>
                <option value="sabado" {{ $horario->dia_semana == 'sabado' ? 'selected' : '' }}>Sábado</option>
                <option value="domingo" {{ $horario->dia_semana == 'domingo' ? 'selected' : '' }}>Domingo</option>
            </select>
        </div>

        <div class="grid grid-cols-2 gap-6">
            <div class="space-y-2">
                <label class="text-sm font-bold text-gray-700 uppercase tracking-wide">Hora Inicio</label>
                <input type="time" name="hora_inicio" value="{{ \Carbon\Carbon::parse($horario->hora_inicio)->format('H:i') }}" class="w-full bg-gray-50 border border-gray-200 text-gray-800 rounded-xl px-4 py-3 focus:ring-2 focus:ring-rose-200 focus:border-rose-400 transition-all" required>
            </div>
            <div class="space-y-2">
                <label class="text-sm font-bold text-gray-700 uppercase tracking-wide">Hora Fin</label>
                <input type="time" name="hora_fin" value="{{ \Carbon\Carbon::parse($horario->hora_fin)->format('H:i') }}" class="w-full bg-gray-50 border border-gray-200 text-gray-800 rounded-xl px-4 py-3 focus:ring-2 focus:ring-rose-200 focus:border-rose-400 transition-all" required>
            </div>
        </div>

        <div class="space-y-2">
            <label class="text-sm font-bold text-gray-700 uppercase tracking-wide">Estado</label>
            <select name="activo" class="w-full bg-gray-50 border border-gray-200 text-gray-800 rounded-xl px-4 py-3 focus:ring-2 focus:ring-rose-200 focus:border-rose-400 transition-all" required>
                <option value="1" {{ $horario->activo ? 'selected' : '' }}>Activo</option>
                <option value="0" {{ !$horario->activo ? 'selected' : '' }}>Inactivo</option>
            </select>
        </div>

        <div class="pt-6 border-t border-gray-100 flex justify-end space-x-3">
            <a href="{{ route('horarios.index') }}" class="px-6 py-3 rounded-xl font-bold text-gray-500 hover:bg-gray-100 transition-colors">Cancelar</a>
            <button type="submit" class="bg-rose-500 hover:bg-rose-600 text-white px-8 py-3 rounded-xl font-bold shadow-lg shadow-rose-200 transition-all">Actualizar Horario</button>
        </div>
    </form>
</div>
@endsection
