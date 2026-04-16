@extends('layouts.app')

@section('title', 'Editar Promotor - Salon Anita')

@section('header')
    <div>
        <h2 class="text-3xl font-extrabold text-gray-800 tracking-tight">Editar Promotor</h2>
        <p class="text-gray-500 font-medium">Actualiza la información de {{ $promotor->nombre }}.</p>
    </div>
@endsection

@section('content')
<div class="max-w-4xl">
    <div class="bg-white rounded-3xl shadow-sm border border-rose-50 overflow-hidden">
        <form action="{{ route('promotores.update', $promotor) }}" method="POST" class="p-8">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-xs font-black text-rose-400 uppercase tracking-widest px-1">Nombre Completo</label>
                    <input type="text" name="nombre" value="{{ old('nombre', $promotor->nombre) }}" required 
                        class="w-full bg-rose-50/30 border border-rose-100 rounded-2xl p-4 text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all"
                        placeholder="Ej. Juan Pérez">
                    @error('nombre') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2">
                    <label class="text-xs font-black text-rose-400 uppercase tracking-widest px-1">Empresa / Marca</label>
                    <input type="text" name="empresa" value="{{ old('empresa', $promotor->empresa) }}"
                        class="w-full bg-rose-50/30 border border-rose-100 rounded-2xl p-4 text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all"
                        placeholder="Ej. L'Oréal">
                    @error('empresa') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2">
                    <label class="text-xs font-black text-rose-400 uppercase tracking-widest px-1">Teléfono de Contacto</label>
                    <input type="text" name="telefono" value="{{ old('telefono', $promotor->telefono) }}"
                        class="w-full bg-rose-50/30 border border-rose-100 rounded-2xl p-4 text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all"
                        placeholder="Ej. +57 300 000 0000">
                    @error('telefono') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-2 space-y-2">
                    <label class="text-xs font-black text-rose-400 uppercase tracking-widest px-1">Notas Adicionales</label>
                    <textarea name="notas" rows="4"
                        class="w-full bg-rose-50/30 border border-rose-100 rounded-2xl p-4 text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all"
                        placeholder="Cualquier información relevante...">{{ old('notas', $promotor->notas) }}</textarea>
                    @error('notas') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="mt-10 flex items-center justify-end space-x-4">
                <a href="{{ route('promotores.index') }}" class="px-6 py-3 rounded-2xl font-bold text-gray-400 hover:text-gray-600 transition-all">Cancelar</a>
                <button type="submit" class="bg-rose-500 hover:bg-rose-600 text-white px-10 py-3 rounded-2xl font-bold transition-all shadow-lg shadow-rose-100">
                    Actualizar Promotor
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
