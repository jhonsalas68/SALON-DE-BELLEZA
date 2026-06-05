@extends('layouts.guest')

@section('title', 'Salón de Belleza Anita - Iniciar Sesión')

@section('content')
    <section class="hero-gradient pt-36 pb-20 px-6 min-h-[80vh] flex items-center justify-center">
        <div class="w-full max-w-md">
            <div class="bg-white p-8 lg:p-12 rounded-[2.5rem] shadow-xl border border-stone-200/50 relative overflow-hidden">
                <div class="absolute -right-10 -bottom-10 w-36 h-36 bg-rose-100/30 rounded-full blur-2xl"></div>
                <div class="absolute -left-10 -top-10 w-36 h-36 bg-amber-100/30 rounded-full blur-2xl"></div>

                <div class="mb-8 text-center relative z-10">
                    <div class="w-14 h-14 bg-gradient-to-br from-amber-500 to-rose-500 rounded-2xl flex items-center justify-center mb-4 shadow-md shadow-rose-100 mx-auto text-white">
                        <i class="fas fa-sign-in-alt text-xl"></i>
                    </div>
                    <h3 class="serif text-2xl lg:text-3xl text-stone-900 leading-tight">Iniciar Sesión</h3>
                    <p class="text-stone-500 text-xs mt-1">Ingresa para gestionar tus citas y compras.</p>
                </div>

                <!-- Alertas de estado / errores -->
                @if (session('status'))
                    <div class="bg-emerald-50 text-emerald-800 text-xs p-4 rounded-2xl mb-6 font-bold text-center border border-emerald-200">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="bg-rose-50 border border-rose-200 text-rose-800 text-xs p-4 rounded-2xl mb-6 font-bold text-center flex flex-col items-center justify-center gap-2">
                        <span>{{ $errors->first() }}</span>
                        @if (session('show_forgot_password') || (old('email') && \Illuminate\Support\Facades\Cache::get('login_failed_' . old('email'), 0) >= 3))
                            <div class="mt-2">
                                <a href="{{ route('password.request', ['email' => old('email')]) }}" class="inline-flex items-center space-x-2 bg-stone-900 hover:bg-rose-500 text-white px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-wider transition-all shadow-sm">
                                    <i class="fas fa-key"></i>
                                    <span>Olvidé mi contraseña</span>
                                </a>
                            </div>
                        @endif
                    </div>
                @endif

                <form action="{{ route('login.post') }}" method="POST" class="space-y-4 relative z-10">
                    @csrf

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
                        <div class="flex justify-between items-center px-1">
                            <label for="password" class="text-[10px] font-black text-stone-400 uppercase tracking-widest">Contraseña</label>
                            <a href="{{ route('password.request') }}" class="text-[10px] font-bold text-amber-600 hover:text-amber-700 hover:underline">¿La olvidaste?</a>
                        </div>
                        <div class="relative">
                            <input type="password" name="password" id="password" required
                                   placeholder="••••••••"
                                   class="w-full pl-11 pr-4 py-3.5 rounded-2xl bg-stone-50 border border-stone-200 focus:border-amber-400 outline-none transition text-sm font-semibold text-stone-700">
                            <i class="fas fa-lock absolute left-4 top-1/2 transform -translate-y-1/2 text-stone-400"></i>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-stone-900 hover:bg-rose-500 text-white font-extrabold py-3.5 rounded-2xl transition-all shadow-md text-xs mt-6">
                        Entrar al Sistema
                    </button>
                </form>

                <div class="mt-8 text-center relative z-10">
                    <p class="text-xs text-stone-500">
                        ¿Aún no tienes cuenta? 
                        <a href="{{ route('register') }}" class="text-amber-600 font-extrabold hover:underline ml-1">Regístrate aquí</a>
                    </p>
                </div>
            </div>
        </div>
    </section>
@endsection
