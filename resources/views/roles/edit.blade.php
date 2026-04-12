@extends('layouts.app')

@section('title', 'Configurar Rol - Salón de Belleza Anita')

@section('header')
<div class="mb-8">
    <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Configurar Rol: {{ $role->name }}</h1>
    <p class="text-gray-500 font-medium">Gestiona los permisos y la descripción del rol.</p>
</div>
@endsection

@section('content')
<div class="max-w-4xl">
    <div class="bg-gray-50 p-10 rounded-[2.5rem] border border-gray-100">
        <form action="{{ route('roles.update', $role) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="space-y-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <label for="name" class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Nombre del Rol</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $role->name) }}" required
                            class="w-full px-4 py-4 bg-white border border-gray-100 rounded-2xl focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 outline-none transition duration-200 shadow-sm"
                            {{ in_array($role->slug, ['administrador', 'cliente']) ? 'readonly' : '' }}>
                    </div>
                    <div>
                        <label for="description" class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Descripción Breve</label>
                        <input type="text" name="description" id="description" value="{{ old('description', $role->description) }}" 
                            class="w-full px-4 py-4 bg-white border border-gray-100 rounded-2xl focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 outline-none transition duration-200 shadow-sm">
                    </div>
                </div>

                <div>
                    <h2 class="text-lg font-black text-gray-800 mb-6 border-b border-gray-100 pb-4">Gestión de Permisos</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($permissions as $p)
                        <label class="flex items-center p-4 bg-white border border-gray-100 rounded-2xl cursor-pointer hover:bg-indigo-50 hover:border-indigo-200 transition-all duration-200 group">
                            <input type="checkbox" name="permissions[]" value="{{ $p->id }}" 
                                class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                                {{ $role->permissions->contains($p->id) ? 'checked' : '' }}>
                            <div class="ml-4">
                                <span class="block text-sm font-bold text-gray-800 group-hover:text-indigo-700 transition-colors">{{ $p->name }}</span>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>

                <div class="pt-8 flex items-center space-x-4">
                    <button type="submit" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-black py-4 rounded-2xl shadow-xl shadow-indigo-100 transform active:scale-95 transition-all">
                        Actualizar Configuración
                    </button>
                    <a href="{{ route('roles.index') }}" class="px-8 py-4 bg-white text-gray-500 font-bold rounded-2xl border border-gray-100 hover:bg-gray-100 transition-all">
                        Cancelar
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
