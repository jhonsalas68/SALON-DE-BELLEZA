@extends('layouts.app')

@section('title', 'Configuraciones - Anita Salon')

@section('header')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-800 tracking-tight">Configuraciones</h1>
    <p class="text-gray-500 text-sm">Gestiona tu seguridad y preferencias de cuenta.</p>
</div>
@endsection

@section('content')
<div class="max-w-4xl">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Info Card -->
        <div class="lg:col-span-1">
            <div class="bg-white p-8 rounded-3xl border border-gray-100 shadow-sm text-center">
                <div class="w-24 h-24 bg-rose-100 rounded-full flex items-center justify-center text-rose-600 font-bold text-3xl mb-4 mx-auto border-4 border-white shadow-lg">
                    {{ substr(auth()->user()->email, 0, 1) }}
                </div>
                <h3 class="text-xl font-bold text-gray-800 truncate">{{ explode('@', auth()->user()->email)[0] }}</h3>
                <p class="text-xs font-bold text-rose-400 uppercase tracking-widest mt-1">{{ auth()->user()->role->name ?? 'Usuario' }}</p>
                <div class="mt-8 pt-8 border-t border-gray-50 text-left space-y-4">
                    <div>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Email</p>
                        <p class="text-sm font-medium text-gray-600">{{ auth()->user()->email }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Settings Form -->
        <div class="lg:col-span-2">
            <div class="bg-white p-10 rounded-[2.5rem] border border-gray-100 shadow-sm">
                <div class="flex items-center space-x-3 mb-8">
                    <div class="w-10 h-10 bg-rose-500 rounded-xl flex items-center justify-center">
                        <i class="fas fa-key text-white"></i>
                    </div>
                    <h2 class="text-xl font-bold text-gray-800">Cambiar Contraseña</h2>
                </div>

                @if ($errors->any())
                    <div class="bg-red-50 text-red-600 p-4 rounded-xl mb-6 text-xs font-bold">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form action="{{ route('profile.password.update') }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')
                    
                    <div>
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest pl-1 mb-2 block">Contraseña Actual</label>
                        <input type="password" name="current_password" required 
                               class="w-full px-6 py-4 rounded-2xl bg-gray-50 border border-gray-100 focus:border-rose-300 outline-none transition text-sm">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest pl-1 mb-2 block">Nueva Contraseña</label>
                            <input type="password" name="new_password" required 
                                   class="w-full px-6 py-4 rounded-2xl bg-gray-50 border border-gray-100 focus:border-rose-300 outline-none transition text-sm">
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest pl-1 mb-2 block">Confirmar Nueva Contraseña</label>
                            <input type="password" name="new_password_confirmation" required 
                                   class="w-full px-6 py-4 rounded-2xl bg-gray-50 border border-gray-100 focus:border-rose-300 outline-none transition text-sm">
                        </div>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="bg-gray-900 hover:bg-rose-500 text-white font-bold px-8 py-4 rounded-2xl transition-all shadow-xl shadow-gray-200 text-sm">
                            Actualizar Contraseña
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection
