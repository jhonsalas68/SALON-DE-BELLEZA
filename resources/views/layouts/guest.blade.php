<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Salón de Belleza Anita - Estilo y Bienestar Premium')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap');
        
        body { 
            font-family: 'Outfit', sans-serif; 
            user-select: none;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
        }

        input, textarea, select {
            user-select: text;
            -webkit-user-select: text;
            -moz-user-select: text;
            -ms-user-select: text;
        }

        .serif {
            font-family: 'Playfair Display', Georgia, serif;
        }

        .glass-nav {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }

        .hero-gradient {
            background: radial-gradient(circle at 10% 20%, rgb(255, 252, 248) 0%, rgb(255, 240, 243) 90%);
        }

        .gold-border {
            border-color: rgba(217, 119, 6, 0.2);
        }

        .gold-gradient {
            background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
        }
    </style>
    @yield('styles')
</head>
<body class="bg-stone-50 text-stone-800 antialiased overflow-x-hidden">

    <!-- Navbar -->
    <header class="glass-nav fixed top-0 w-full z-40 border-b border-stone-100 shadow-sm transition-all duration-300">
        <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
            <!-- Logo -->
            <a href="{{ route('landing') }}" class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-gradient-to-br from-amber-500 to-rose-500 rounded-xl flex items-center justify-center shadow-md">
                    <i class="fas fa-spa text-white text-lg"></i>
                </div>
                <div>
                    <h1 class="text-xl font-black tracking-tight text-stone-900 leading-none">Salon Anita</h1>
                    <p class="text-[9px] font-black uppercase tracking-widest text-amber-600 mt-1">Estilo & Belleza Premium</p>
                </div>
            </a>

            <!-- Navegación Central -->
            <nav class="hidden md:flex items-center space-x-8 text-sm font-bold text-stone-600">
                <a href="{{ route('landing') }}#inicio" class="hover:text-amber-600 transition-colors">Inicio</a>
                @if(!isset($promociones) || !$promociones->isEmpty())
                    <a href="{{ route('landing') }}#promociones" class="hover:text-amber-600 transition-colors">Promociones</a>
                @endif
                <a href="{{ route('landing') }}#servicios" class="hover:text-amber-600 transition-colors">Servicios</a>
                <a href="{{ route('landing') }}#productos" class="hover:text-amber-600 transition-colors">Tienda</a>
                @auth
                    <a href="{{ route('landing') }}#portal" class="text-amber-600 hover:text-amber-700 flex items-center space-x-1">
                        <i class="fas fa-user-circle"></i>
                        <span>Mi Portal</span>
                    </a>
                @endauth
            </nav>

            <!-- Acciones Derecha -->
            <div class="flex items-center space-x-4">
                @auth
                    <div class="flex items-center space-x-3 bg-stone-100/80 px-3.5 py-2 rounded-2xl border border-stone-200/50">
                        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-amber-500 to-amber-600 flex items-center justify-center text-white font-bold text-xs shadow-sm">
                            {{ strtoupper(substr(auth()->user()->name ?? auth()->user()->email, 0, 1)) }}
                        </div>
                        <div class="hidden sm:block text-left leading-tight pr-2">
                            <p class="text-xs font-black text-stone-800">{{ auth()->user()->name ?? 'Cliente' }}</p>
                            <p class="text-[8px] font-bold text-amber-600 uppercase tracking-widest">{{ auth()->user()->role->name ?? 'Usuario' }}</p>
                        </div>
                        
                        <!-- Si es administrativo, botón para ir al dashboard -->
                        @if(!auth()->user()->hasRole('cliente'))
                            <a href="{{ route('dashboard') }}" class="text-xs bg-indigo-500 hover:bg-indigo-600 text-white font-extrabold px-3 py-1.5 rounded-xl transition-all shadow-sm">
                                <i class="fas fa-chart-line mr-1"></i> Admin
                            </a>
                        @endif

                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-stone-400 hover:text-rose-500 text-xs transition-colors p-1" title="Cerrar Sesión">
                                <i class="fas fa-sign-out-alt"></i>
                            </button>
                        </form>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-bold text-stone-600 hover:text-amber-600 transition-colors">Iniciar Sesión</a>
                    <a href="{{ route('register') }}" class="text-sm bg-gradient-to-r from-amber-500 to-rose-500 hover:from-amber-600 hover:to-rose-600 text-white font-black px-5 py-2.5 rounded-xl transition-all shadow-md shadow-rose-100">Registrarse</a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Alertas Flash -->
    <div class="fixed top-24 left-1/2 transform -translate-x-1/2 z-50 w-full max-w-xl px-4 pointer-events-none">
        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-850 px-6 py-4 rounded-2xl shadow-xl flex items-center justify-between pointer-events-auto transition-all duration-500 ease-out transform translate-y-0 opacity-100 mb-3 alert-box">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-check-circle text-emerald-500 text-xl"></i>
                    <p class="font-extrabold text-sm">{{ session('success') }}</p>
                </div>
                <button onclick="dismissAlert(this)" class="text-emerald-400 hover:text-emerald-600 transition-colors ml-4 focus:outline-none">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endif
        @if(session('error'))
            <div class="bg-rose-50 border border-rose-200 text-rose-855 px-6 py-4 rounded-2xl shadow-xl flex items-center justify-between pointer-events-auto transition-all duration-500 ease-out transform translate-y-0 opacity-100 mb-3 alert-box">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-exclamation-circle text-rose-500 text-xl"></i>
                    <p class="font-extrabold text-sm">{{ session('error') }}</p>
                </div>
                <button onclick="dismissAlert(this)" class="text-rose-400 hover:text-rose-600 transition-colors ml-4 focus:outline-none">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endif
    </div>

    <!-- Contenido Principal -->
    <div class="min-h-screen flex flex-col justify-between">
        <main class="flex-1">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-stone-900 text-stone-400 py-12 border-t border-stone-800 px-6">
            <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="space-y-4">
                    <div class="flex items-center space-x-3 text-white">
                        <div class="w-8 h-8 bg-gradient-to-br from-amber-500 to-rose-500 rounded-lg flex items-center justify-center">
                            <i class="fas fa-spa text-xs"></i>
                        </div>
                        <span class="text-lg font-black tracking-tight">Salon Anita</span>
                    </div>
                    <p class="text-xs leading-relaxed text-stone-500">Resaltamos tu estilo y cuidamos tu bienestar con los tratamientos estéticos más modernos y productos de calidad.</p>
                </div>
                
                <div class="space-y-3">
                    <h5 class="text-sm font-black text-stone-200 uppercase tracking-widest">Horarios de Atención</h5>
                    <ul class="text-xs space-y-1 text-stone-500 font-medium">
                        <li>Lunes a Viernes: 08:30 AM - 06:30 PM</li>
                        <li>Sábados: 09:00 AM - 05:00 PM</li>
                        <li>Domingos: Cerrado</li>
                    </ul>
                </div>

                <div class="space-y-3">
                    <h5 class="text-sm font-black text-stone-200 uppercase tracking-widest">Contacto</h5>
                    <ul class="text-xs space-y-1 text-stone-500 font-medium">
                        <li><i class="fas fa-phone mr-1.5"></i> +591 71234567</li>
                        <li><i class="fas fa-map-marker-alt mr-1.5"></i> Av. Arce #1234, La Paz, Bolivia</li>
                        <li><i class="fas fa-envelope mr-1.5"></i> contacto@salonanita.com</li>
                    </ul>
                </div>

                <div class="space-y-3">
                    <h5 class="text-sm font-black text-stone-200 uppercase tracking-widest">Desarrollado Para</h5>
                    <p class="text-xs text-stone-500 leading-relaxed font-semibold">Salón de Belleza SI. Sistema web integrado para agendamiento, inventario y comisiones.</p>
                </div>
            </div>
            <div class="max-w-7xl mx-auto mt-12 pt-8 border-t border-stone-800 text-center text-xs text-stone-600 font-bold">
                <p>&copy; {{ date('Y') }} Salón de Belleza Anita. Todos los derechos reservados.</p>
            </div>
        </footer>
    </div>

    <!-- Scripts Globales -->
    <script>
        function dismissAlert(button) {
            const alert = button.closest('.alert-box');
            if (alert) {
                alert.style.transition = 'all 0.5s ease';
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-20px)';
                alert.style.marginTop = '-' + alert.offsetHeight + 'px';
                alert.style.marginBottom = '0';
                alert.style.paddingTop = '0';
                alert.style.paddingBottom = '0';
                alert.style.height = '0';
                alert.style.overflow = 'hidden';
                alert.style.border = 'none';
                setTimeout(() => alert.remove(), 500);
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert-box');
            alerts.forEach(alert => {
                setTimeout(() => {
                    if (alert.parentNode) {
                        alert.style.transition = 'all 0.5s ease';
                        alert.style.opacity = '0';
                        alert.style.transform = 'translateY(-20px)';
                        alert.style.marginTop = '-' + alert.offsetHeight + 'px';
                        alert.style.marginBottom = '0';
                        alert.style.paddingTop = '0';
                        alert.style.paddingBottom = '0';
                        alert.style.height = '0';
                        alert.style.overflow = 'hidden';
                        alert.style.border = 'none';
                        setTimeout(() => alert.remove(), 500);
                    }
                }, 10000); // 10 segundos
            });
        });

        // Bloquear copiar, cortar y click derecho
        document.addEventListener('copy', e => e.preventDefault());
        document.addEventListener('cut', e => e.preventDefault());
        document.addEventListener('contextmenu', e => e.preventDefault());
        document.addEventListener('dragstart', e => e.preventDefault());

        // Bloquear combinaciones de teclado Ctrl+C, Ctrl+A, Ctrl+X, Ctrl+U y F12
        document.addEventListener('keydown', e => {
            if (e.ctrlKey && (e.key === 'c' || e.key === 'C' || e.key === 'a' || e.key === 'A' || e.key === 'x' || e.key === 'X' || e.key === 'u' || e.key === 'U')) {
                e.preventDefault();
            }
            if (e.key === 'F12') {
                e.preventDefault();
            }
        });
    </script>
    @yield('scripts')
</body>
</html>
