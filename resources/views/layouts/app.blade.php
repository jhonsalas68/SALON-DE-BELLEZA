<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Salón de Belleza Anita')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap');
        
        body { 
            font-family: 'Outfit', sans-serif; 
            background: #fef2f2; 
            color: #374151; 
        }
        
        .sidebar { 
            background: #ffffff; 
            border-right: 1px solid #fee2e2;
        }

        .nav-link { 
            transition: all 0.2s ease;
            border-radius: 0.75rem;
            margin-bottom: 0.25rem;
            color: #6b7280;
            font-weight: 500;
        }

        .nav-link:hover { 
            background: #fff1f2; 
            color: #be123c;
        }

        .nav-link.active { 
            background: #fb7185;
            color: white;
            box-shadow: 0 4px 6px -1px rgba(251, 113, 133, 0.4);
        }

        .content-area { 
            background: #ffffff;
            border-radius: 1.5rem 0 0 1.5rem; 
            margin-top: 1rem;
            margin-bottom: 1rem;
        }

        .glass-avatar { 
            background: #fff5f5; 
            border: 1px solid #fee2e2;
        }

        @media (max-width: 1024px) { .content-area { border-radius: 0; margin: 0; } }
    </style>
    @yield('styles')
</head>
<body class="bg-white flex select-none">
    <!-- Sidebar -->
    <aside class="w-64 sidebar min-h-screen p-6 hidden lg:flex flex-col">
        <div class="flex items-center space-x-3 mb-10 px-2">
            <div class="w-10 h-10 bg-rose-500 rounded-xl flex items-center justify-center shadow-sm">
                <i class="fas fa-spa text-white"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold text-gray-800 tracking-tight">Anita Salon</h1>
                <p class="text-[10px] text-rose-400 font-bold uppercase tracking-widest">Belleza Premium</p>
            </div>
        </div>

        <nav class="space-y-1">
            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-4 px-2">Menu</p>
            <a href="{{ route('dashboard') }}" class="nav-link flex items-center space-x-3 p-3 {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fas fa-home"></i>
                <span class="font-medium">Inicio</span>
            </a>
            
            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-8 mb-4 px-2">Administración</p>
            
            @if(auth()->user()->hasPermission('manage_users'))
            <a href="{{ route('users.index') }}" class="nav-link flex items-center space-x-3 p-3 {{ request()->routeIs('users.*') ? 'active' : '' }}">
                <i class="fas fa-users"></i>
                <span class="font-medium">Usuarios</span>
            </a>
            @endif

            @if(auth()->user()->hasPermission('manage_roles'))
            <a href="{{ route('roles.index') }}" class="nav-link flex items-center space-x-3 p-3 {{ request()->routeIs('roles.*') ? 'active' : '' }}">
                <i class="fas fa-user-lock"></i>
                <span class="font-medium">Accesos</span>
            </a>
            @endif

            @if(auth()->user()->hasPermission('view_audit_log'))
            <a href="{{ route('activity_logs.index') }}" class="nav-link flex items-center space-x-3 p-3 {{ request()->routeIs('activity_logs.*') ? 'active' : '' }}">
                <i class="fas fa-list-ul"></i>
                <span class="font-medium">Bitácora</span>
            </a>
            @endif

            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-8 mb-4 px-2">Agenda</p>
            <a href="#" class="nav-link flex items-center space-x-3 p-3 opacity-50 cursor-not-allowed">
                <i class="fas fa-calendar"></i>
                <span class="font-medium">Citas</span>
            </a>

            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-8 mb-4 px-2">Mi Cuenta</p>
            <a href="{{ route('profile.index') }}" class="nav-link flex items-center space-x-3 p-3 {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                <i class="fas fa-cog"></i>
                <span class="font-medium">Configuraciones</span>
            </a>

            <div class="mt-8 pt-8 border-t border-rose-50">
                <div class="p-4 bg-rose-50/50 rounded-2xl flex items-center space-x-3 mb-4">
                    <div class="w-8 h-8 rounded-full bg-rose-500 flex items-center justify-center text-white font-bold text-xs uppercase">
                        {{ substr(auth()->user()->email, 0, 1) }}
                    </div>
                    <div class="overflow-hidden">
                        <p class="text-xs font-bold text-gray-800 truncate">{{ explode('@', auth()->user()->email)[0] }}</p>
                        <p class="text-[9px] text-gray-400 font-black uppercase tracking-widest">{{ auth()->user()->role->name ?? 'User' }}</p>
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full bg-gray-900 hover:bg-rose-600 text-white p-3 rounded-xl transition-all font-bold flex items-center justify-center space-x-2 text-xs shadow-lg shadow-gray-200">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Salir del Sistema</span>
                    </button>
                </form>
            </div>
        </nav>
    </aside>

    <!-- Content Area -->
    <main class="flex-1 bg-gray-50 min-h-screen content-area overflow-y-auto">
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
