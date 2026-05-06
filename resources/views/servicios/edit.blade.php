@extends('layouts.app')

@section('title', 'Editar Servicio - Salon Anita')

@section('header')
    <div class="flex items-center space-x-4">
        <a href="{{ route('servicios.index') }}" class="text-gray-400 hover:text-rose-500 transition-colors">
            <i class="fas fa-arrow-left text-xl"></i>
        </a>
        <div>
            <h2 class="text-3xl font-extrabold text-gray-800 tracking-tight">Editar Servicio</h2>
            <p class="text-gray-500 font-medium">Modificar los detalles del servicio.</p>
        </div>
    </div>
@endsection

@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-3xl shadow-sm border border-rose-50 p-8">
    <form action="{{ route('servicios.update', $servicio) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')
        
        <div class="space-y-2">
            <label class="text-sm font-bold text-gray-700 uppercase tracking-wide">Nombre del Servicio</label>
            <input type="text" name="nombre" value="{{ $servicio->nombre }}" class="w-full bg-gray-50 border border-gray-200 text-gray-800 rounded-xl px-4 py-3 focus:ring-2 focus:ring-rose-200 focus:border-rose-400 transition-all" required>
        </div>

        <div class="space-y-2">
            <label class="text-sm font-bold text-gray-700 uppercase tracking-wide">Descripción (Opcional)</label>
            <textarea name="descripcion" rows="3" class="w-full bg-gray-50 border border-gray-200 text-gray-800 rounded-xl px-4 py-3 focus:ring-2 focus:ring-rose-200 focus:border-rose-400 transition-all">{{ $servicio->descripcion }}</textarea>
        </div>

        <div class="grid grid-cols-2 gap-6">
            <div class="space-y-2">
                <label class="text-sm font-bold text-gray-700 uppercase tracking-wide">Duración (Minutos)</label>
                <input type="number" name="duracion_minutos" value="{{ $servicio->duracion_minutos }}" min="5" class="w-full bg-gray-50 border border-gray-200 text-gray-800 rounded-xl px-4 py-3 focus:ring-2 focus:ring-rose-200 focus:border-rose-400 transition-all" required>
            </div>
            <div class="space-y-2">
                <label class="text-sm font-bold text-gray-700 uppercase tracking-wide">Precio (Bs.)</label>
                <input type="number" step="0.01" name="precio" value="{{ $servicio->precio }}" class="w-full bg-gray-50 border border-gray-200 text-gray-800 rounded-xl px-4 py-3 focus:ring-2 focus:ring-rose-200 focus:border-rose-400 transition-all" required>
            </div>
        </div>

        <div class="space-y-2">
            <label class="text-sm font-bold text-gray-700 uppercase tracking-wide">Estado</label>
            <select name="activo" class="w-full bg-gray-50 border border-gray-200 text-gray-800 rounded-xl px-4 py-3 focus:ring-2 focus:ring-rose-200 focus:border-rose-400 transition-all" required>
                <option value="1" {{ $servicio->activo ? 'selected' : '' }}>Activo</option>
                <option value="0" {{ !$servicio->activo ? 'selected' : '' }}>Inactivo</option>
            </select>
        </div>

        <div class="pt-6 border-t border-gray-100 flex justify-end space-x-3">
            <a href="{{ route('servicios.index') }}" class="px-6 py-3 rounded-xl font-bold text-gray-500 hover:bg-gray-100 transition-colors">Cancelar</a>
            <button type="submit" class="bg-rose-500 hover:bg-rose-600 text-white px-8 py-3 rounded-xl font-bold shadow-lg shadow-rose-200 transition-all">Actualizar Servicio</button>
        </div>
    </form>
</div>
@endsection
