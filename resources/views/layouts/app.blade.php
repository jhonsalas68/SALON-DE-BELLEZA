<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Salón de Belleza Anita')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Poppins', sans-serif; background: #f3f4f6; }
        .sidebar { background: linear-gradient(180deg, #1e1b4b 0%, #312e81 100%); transition: all 0.3s ease; }
        .glass-card { background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.2); }
        .nav-link { transition: all 0.2s ease; border-radius: 0.75rem; margin-bottom: 0.5rem; }
        .nav-link:hover { background: rgba(255, 255, 255, 0.1); transform: translateX(5px); }
        .nav-link.active { background: #6366f1; box-shadow: 0 4px 12px rgba(99, 102, 241, 0.4); }
        .content-area { border-radius: 2rem 0 0 0; }
        @media (max-width: 1024px) { .content-area { border-radius: 0; } }
    </style>
    @yield('styles')
</head>
<body class="bg-indigo-950 flex">
    <!-- Sidebar -->
    <aside class="w-72 min-h-screen text-white p-6 sticky top-0 hidden lg:block overflow-y-auto">
        <div class="flex items-center space-x-3 mb-10 px-2">
            <div class="w-10 h-10 bg-gradient-to-br from-pink-500 to-purple-500 rounded-xl flex items-center justify-center shadow-lg">
                <i class="fas fa-spa text-white"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold tracking-tight">Anita Salon</h1>
                <p class="text-[10px] text-indigo-300 font-bold uppercase tracking-widest">Premium Beauty</p>
            </div>
        </div>

        <nav>
            <p class="text-[11px] text-indigo-300 font-bold uppercase tracking-widest mb-4 px-2 opacity-60">Menú Principal</p>
            <a href="{{ route('dashboard') }}" class="nav-link flex items-center space-x-3 p-3 {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fas fa-th-large"></i>
                <span>Dashboard</span>
            </a>
            
            <p class="text-[11px] text-indigo-300 font-bold uppercase tracking-widest mt-8 mb-4 px-2 opacity-60">Administración</p>
            
            @if(auth()->user()->hasRole('administrador') || auth()->user()->hasRole('recepcionista'))
            <a href="{{ route('users.index') }}" class="nav-link flex items-center space-x-3 p-3 {{ request()->routeIs('users.*') ? 'active' : '' }}">
                <i class="fas fa-users-cog"></i>
                <span>Usuarios</span>
            </a>
            @endif

            @if(auth()->user()->hasRole('administrador'))
            <a href="{{ route('roles.index') }}" class="nav-link flex items-center space-x-3 p-3 {{ request()->routeIs('roles.*') ? 'active' : '' }}">
                <i class="fas fa-user-shield"></i>
                <span>Roles y Permisos</span>
            </a>
            <a href="{{ route('activity_logs.index') }}" class="nav-link flex items-center space-x-3 p-3 {{ request()->routeIs('activity_logs.*') ? 'active' : '' }}">
                <i class="fas fa-history"></i>
                <span>Bitácora</span>
            </a>
            @endif

            <p class="text-[11px] text-indigo-300 font-bold uppercase tracking-widest mt-8 mb-4 px-2 opacity-60">Servicios</p>
            <a href="#" class="nav-link flex items-center space-x-3 p-3 text-white/60 cursor-not-allowed">
                <i class="fas fa-calendar-check text-indigo-400"></i>
                <span>Citas (Próximamente)</span>
            </a>
            <a href="#" class="nav-link flex items-center space-x-3 p-3 text-white/60 cursor-not-allowed">
                <i class="fas fa-cut text-indigo-400"></i>
                <span>Servicios (Próximamente)</span>
            </a>
        </nav>

        <div class="absolute bottom-10 left-6 right-6">
            <div class="bg-white/10 p-4 rounded-2xl flex items-center space-x-3">
                <div class="w-10 h-10 rounded-full bg-indigo-500 overflow-hidden border-2 border-indigo-400">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->email) }}&background=6366f1&color=fff" alt="User">
                </div>
                <div class="overflow-hidden">
                    <p class="text-xs font-bold truncate">{{ auth()->user()->email }}</p>
                    <p class="text-[10px] text-indigo-300">{{ auth()->user()->role->name ?? 'Usuario' }}</p>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST" class="mt-4">
                @csrf
                <button type="submit" class="w-full bg-red-500/20 hover:bg-red-500 text-red-400 hover:text-white p-3 rounded-xl transition font-bold flex items-center justify-center space-x-2">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Cerrar Sesión</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Content Area -->
    <main class="flex-1 bg-white min-h-screen content-area shadow-2xl overflow-y-auto">
        <header class="p-8 pb-0">
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-xl animate-fade-in">
                    <p class="text-sm font-bold">{{ session('success') }}</p>
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-xl animate-fade-in">
                    <p class="text-sm font-bold">{{ session('error') }}</p>
                </div>
            @endif
            @yield('header')
        </header>

        <div class="p-8 pt-4">
            @yield('content')
        </div>
    </main>

    @yield('scripts')
</body>
</html>
