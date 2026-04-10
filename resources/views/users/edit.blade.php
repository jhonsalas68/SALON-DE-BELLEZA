@extends('layouts.app')

@section('title', 'Editar Usuario - Salón de Belleza Anita')

@section('header')
<div class="mb-8">
    <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Editar Usuario</h1>
    <p class="text-gray-500 font-medium">Modifica la información del perfil del usuario.</p>
</div>
@endsection

@section('content')
<div class="max-w-2xl">
    <div class="bg-gray-50 p-10 rounded-[2.5rem] border border-gray-100">
        <form action="{{ route('users.update', $user) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="space-y-8">
                <div>
                    <label for="email" class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Correo Electrónico</label>
                    <div class="relative">
                        <i class="far fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                            class="w-full pl-12 pr-4 py-4 bg-white border border-gray-100 rounded-2xl focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 outline-none transition duration-200 shadow-sm">
                    </div>
                    @error('email') <p class="text-red-500 text-xs mt-2 font-bold">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="password" class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Nueva Contraseña (Opcional)</label>
                    <div class="relative">
                        <i class="fas fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input type="password" name="password" id="password"
                            class="w-full pl-12 pr-4 py-4 bg-white border border-gray-100 rounded-2xl focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 outline-none transition duration-200 shadow-sm"
                            placeholder="Dejar vacío para no cambiar">
                    </div>
                    @error('password') <p class="text-red-500 text-xs mt-2 font-bold">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="role_id" class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Rol del Sistema</label>
                    <div class="relative">
                        <i class="fas fa-user-shield absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <select name="role_id" id="role_id" required
                            class="w-full pl-12 pr-4 py-4 bg-white border border-gray-100 rounded-2xl focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 outline-none transition duration-200 shadow-sm appearance-none">
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                            @endforeach
                        </select>
                        <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                    </div>
                    @error('role_id') <p class="text-red-500 text-xs mt-2 font-bold">{{ $message }}</p> @enderror
                </div>

                <div class="pt-4 flex items-center space-x-4">
                    <button type="submit" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-black py-4 rounded-2xl shadow-xl shadow-indigo-100 transform active:scale-95 transition-all">
                        Guardar Cambios
                    </button>
                    <a href="{{ route('users.index') }}" class="px-8 py-4 bg-white text-gray-500 font-bold rounded-2xl border border-gray-100 hover:bg-gray-100 transition-all">
                        Cancelar
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
