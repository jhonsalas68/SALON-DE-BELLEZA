@extends('layouts.guest')

@section('title', 'Salón de Belleza Anita - Registrarse')

@section('content')
    <section class="hero-gradient pt-36 pb-20 px-6 min-h-[80vh] flex items-center justify-center">
        <div class="w-full max-w-md">
            <div class="bg-white p-8 lg:p-12 rounded-[2.5rem] shadow-xl border border-stone-200/50 relative overflow-hidden">
                <div class="absolute -right-10 -bottom-10 w-36 h-36 bg-rose-100/30 rounded-full blur-2xl"></div>
                <div class="absolute -left-10 -top-10 w-36 h-36 bg-amber-100/30 rounded-full blur-2xl"></div>

                <div class="mb-8 text-center relative z-10">
                    <div class="w-14 h-14 bg-gradient-to-br from-amber-500 to-rose-500 rounded-2xl flex items-center justify-center mb-4 shadow-md shadow-rose-100 mx-auto text-white">
                        <i class="fas fa-user-plus text-xl"></i>
                    </div>
                    <h3 class="serif text-2xl lg:text-3xl text-stone-900 leading-tight">Registrarse</h3>
                    <p class="text-stone-500 text-xs mt-1">Crea tu cuenta para reservar servicios y comprar online.</p>
                </div>

                @if ($errors->any())
                    <div class="bg-rose-50 border border-rose-200 text-rose-800 text-xs p-4 rounded-2xl mb-6 font-bold text-center">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('register.post') }}" method="POST" class="space-y-4 relative z-10">
                    @csrf

                    <div class="space-y-1">
                        <label for="name" class="text-[10px] font-black text-stone-400 uppercase tracking-widest pl-1">Nombre Completo</label>
                        <div class="relative">
                            <input type="text" name="name" id="name" required value="{{ old('name') }}"
                                   placeholder="Tu Nombre Completo"
                                   class="w-full pl-11 pr-4 py-3.5 rounded-2xl bg-stone-50 border border-stone-200 focus:border-amber-400 outline-none transition text-sm font-semibold text-stone-700">
                            <i class="fas fa-user absolute left-4 top-1/2 transform -translate-y-1/2 text-stone-400"></i>
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label for="email" class="text-[10px] font-black text-stone-400 uppercase tracking-widest pl-1">Correo Electrónico</label>
                        <div class="relative">
                            <input type="email" name="email" id="email" required value="{{ old('email') }}"
                                   placeholder="tu@correo.com"
                                   class="w-full pl-11 pr-4 py-3.5 rounded-2xl bg-stone-50 border border-stone-200 focus:border-amber-400 outline-none transition text-sm font-semibold text-stone-700">
                            <i class="fas fa-envelope absolute left-4 top-1/2 transform -translate-y-1/2 text-stone-400"></i>
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label for="password" class="text-[10px] font-black text-stone-400 uppercase tracking-widest pl-1">Contraseña</label>
                        <div class="relative">
                            <input type="password" name="password" id="password" required
                                   placeholder="••••••••"
                                   class="w-full pl-11 pr-4 py-3.5 rounded-2xl bg-stone-50 border border-stone-200 focus:border-amber-400 outline-none transition text-sm font-semibold text-stone-700">
                            <i class="fas fa-lock absolute left-4 top-1/2 transform -translate-y-1/2 text-stone-400"></i>
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label for="password_confirmation" class="text-[10px] font-black text-stone-400 uppercase tracking-widest pl-1">Confirmar Contraseña</label>
                        <div class="relative">
                            <input type="password" name="password_confirmation" id="password_confirmation" required
                                   placeholder="••••••••"
                                   class="w-full pl-11 pr-4 py-3.5 rounded-2xl bg-stone-50 border border-stone-200 focus:border-amber-400 outline-none transition text-sm font-semibold text-stone-700">
                            <i class="fas fa-lock absolute left-4 top-1/2 transform -translate-y-1/2 text-stone-400"></i>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-stone-900 hover:bg-rose-500 text-white font-extrabold py-3.5 rounded-2xl transition-all shadow-md text-xs mt-6">
                        Crear Cuenta
                    </button>
                </form>

                <div class="mt-8 text-center relative z-10">
                    <p class="text-xs text-stone-500">
                        ¿Ya tienes cuenta? 
                        <a href="{{ route('login') }}" class="text-amber-600 font-extrabold hover:underline ml-1">Inicia sesión aquí</a>
                    </p>
                </div>
            </div>
        </div>
    </section>
@endsection
