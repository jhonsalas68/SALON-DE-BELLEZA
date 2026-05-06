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
        }

        .nav-link { 
            transition: all 0.2s ease;
            border-radius: 0.75rem;
            margin-bottom: 0.25rem;
            color: #6b7280;
            font-weight: 600;
        }

        .nav-link:hover { 
            background: #fff1f2; 
            color: #e11d48;
        }

        .nav-link.active { 
            background: #f43f5e;
            color: white;
            box-shadow: 0 4px 14px 0 rgba(244, 63, 94, 0.39);
        }

        /* Ocultar barra de desplazamiento en el sidebar pero permitir scroll */
        .sidebar-scroll::-webkit-scrollbar { display: none; }
        .sidebar-scroll { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
    @yield('styles')
</head>
<body class="bg-gray-50 text-gray-800 font-sans antialiased overflow-hidden flex h-screen select-none">

    <!-- Sidebar Overlay para móviles -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-40 hidden lg:hidden transition-opacity opacity-0 duration-300" onclick="toggleSidebar()"></div>

    <!-- Sidebar -->
    <aside id="sidebar" class="w-72 bg-white h-screen flex flex-col fixed inset-y-0 left-0 z-50 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out border-r border-gray-100 shadow-sm shadow-rose-50/50">
        <!-- Logo Header -->
        <div class="px-6 py-6 flex items-center justify-between shrink-0">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-gradient-to-br from-rose-400 to-rose-600 rounded-xl flex items-center justify-center shadow-lg shadow-rose-200">
                    <i class="fas fa-spa text-white"></i>
                </div>
                <div>
                    <h1 class="text-xl font-extrabold text-gray-900 tracking-tight">Salon Anita</h1>
                    <p class="text-[10px] text-rose-500 font-black uppercase tracking-widest">Belleza Premium</p>
                </div>
            </div>
            <!-- Botón de cerrar (Solo móvil) -->
            <button onclick="toggleSidebar()" class="lg:hidden w-8 h-8 flex items-center justify-center text-gray-400 hover:text-rose-500 bg-gray-50 hover:bg-rose-50 rounded-lg transition-colors">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

        <!-- Links del Menú -->
        <div class="flex-1 overflow-y-auto px-4 sidebar-scroll pb-6">
            <nav class="space-y-1">
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-3 px-2 mt-2">Navegación</p>
                <a href="{{ route('dashboard') }}" class="nav-link flex items-center space-x-3 p-3 {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-home w-5 text-center"></i>
                    <span class="text-sm">Inicio</span>
                </a>
                
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-8 mb-3 px-2">Administración</p>
                
                @if(auth()->user()->hasPermission('manage_users'))
                <a href="{{ route('users.index') }}" class="nav-link flex items-center space-x-3 p-3 {{ request()->routeIs('users.*') ? 'active' : '' }}">
                    <i class="fas fa-users w-5 text-center"></i>
                    <span class="text-sm">Usuarios</span>
                </a>
                @endif

                @if(auth()->user()->hasPermission('manage_roles'))
                <a href="{{ route('roles.index') }}" class="nav-link flex items-center space-x-3 p-3 {{ request()->routeIs('roles.*') ? 'active' : '' }}">
                    <i class="fas fa-user-shield w-5 text-center"></i>
                    <span class="text-sm">Accesos</span>
                </a>
                @endif

                @if(auth()->user()->hasPermission('view_audit_log'))
                <a href="{{ route('activity_logs.index') }}" class="nav-link flex items-center space-x-3 p-3 {{ request()->routeIs('activity_logs.*') ? 'active' : '' }}">
                    <i class="fas fa-clipboard-list w-5 text-center"></i>
                    <span class="text-sm">Bitácora</span>
                </a>
                @endif

                @if(auth()->user()->hasPermission('manage_inventory') || auth()->user()->hasPermission('manage_services'))
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-8 mb-3 px-2">Catálogo e Inventario</p>
                @endif

                @if(auth()->user()->hasPermission('manage_services'))
                <a href="{{ route('servicios.index') }}" class="nav-link flex items-center space-x-3 p-3 {{ request()->routeIs('servicios.*') ? 'active' : '' }}">
                    <i class="fas fa-cut w-5 text-center"></i>
                    <span class="text-sm">Servicios</span>
                </a>
                @endif

                @if(auth()->user()->hasPermission('manage_inventory'))
                <a href="{{ route('productos.index') }}" class="nav-link flex items-center space-x-3 p-3 {{ request()->routeIs('productos.*') ? 'active' : '' }}">
                    <i class="fas fa-box-open w-5 text-center"></i>
                    <span class="text-sm">Productos</span>
                </a>
                <a href="{{ route('promotores.index') }}" class="nav-link flex items-center space-x-3 p-3 {{ request()->routeIs('promotores.*') ? 'active' : '' }}">
                    <i class="fas fa-truck w-5 text-center"></i>
                    <span class="text-sm">Proveedores</span>
                </a>
                @endif

                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-8 mb-3 px-2">Agenda y Clientes</p>
                @if(auth()->user()->hasPermission('manage_appointments'))
                <a href="{{ route('clientes.index') }}" class="nav-link flex items-center space-x-3 p-3 {{ request()->routeIs('clientes.*') ? 'active' : '' }}">
                    <i class="fas fa-address-book w-5 text-center"></i>
                    <span class="text-sm">Directorio Clientes</span>
                </a>
                @endif
                <a href="#" class="nav-link flex items-center space-x-3 p-3 opacity-50 cursor-not-allowed">
                    <i class="fas fa-calendar-alt w-5 text-center"></i>
                    <span class="text-sm">Citas</span>
                </a>
                @if(auth()->user()->hasPermission('view_schedules') || auth()->user()->hasPermission('manage_schedules'))
                <a href="{{ route('horarios.index') }}" class="nav-link flex items-center space-x-3 p-3 {{ request()->routeIs('horarios.*') ? 'active' : '' }}">
                    <i class="fas fa-clock w-5 text-center"></i>
                    <span class="text-sm">Horarios</span>
                </a>
                @endif

                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-8 mb-3 px-2">Mi Cuenta</p>
                <a href="{{ route('profile.index') }}" class="nav-link flex items-center space-x-3 p-3 {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                    <i class="fas fa-cog w-5 text-center"></i>
                    <span class="text-sm">Ajustes</span>
                </a>
            </nav>
        </div>

        <!-- Perfil Usuario y Salir -->
        <div class="p-4 border-t border-gray-100 bg-white shrink-0">
            <div class="p-3 bg-gray-50 rounded-2xl flex items-center space-x-3 mb-3 border border-gray-100">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-600 flex items-center justify-center text-white font-bold text-sm shadow-sm">
                    {{ strtoupper(substr(auth()->user()->email, 0, 1)) }}
                </div>
                <div class="overflow-hidden flex-1">
                    <p class="text-sm font-extrabold text-gray-800 truncate" title="{{ auth()->user()->email }}">{{ explode('@', auth()->user()->email)[0] }}</p>
                    <p class="text-[10px] text-indigo-500 font-black uppercase tracking-widest">{{ auth()->user()->role->name ?? 'Usuario' }}</p>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full bg-gray-900 hover:bg-rose-500 text-white p-3.5 rounded-xl transition-all duration-200 font-bold flex items-center justify-center space-x-2 text-sm shadow-md">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Cerrar Sesión</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Wrapper Principal del Contenido -->
    <div class="flex-1 flex flex-col min-w-0 bg-[#f8fafc] lg:ml-72 h-screen transition-all duration-300">
        
        <!-- Mobile Header (Visible solo en pantallas pequeñas) -->
        <header class="lg:hidden sticky top-0 z-30 flex items-center justify-between bg-white/80 backdrop-blur-md px-6 py-4 border-b border-gray-200 shadow-sm shrink-0">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 bg-gradient-to-br from-rose-400 to-rose-600 rounded-lg flex items-center justify-center shadow-sm">
                    <i class="fas fa-spa text-white text-[10px]"></i>
                </div>
                <h1 class="text-lg font-extrabold text-gray-900 tracking-tight">Salon Anita</h1>
            </div>
            <button onclick="toggleSidebar()" class="text-gray-500 hover:text-rose-500 focus:outline-none bg-gray-100 hover:bg-rose-50 w-10 h-10 rounded-xl flex items-center justify-center transition-colors">
                <i class="fas fa-bars text-lg"></i>
            </button>
        </header>

        <!-- Área de Scroll del Contenido Principal -->
        <main class="flex-1 overflow-y-auto w-full relative scroll-smooth">
            <div class="px-6 py-8 lg:px-10 lg:py-10 max-w-7xl mx-auto min-h-full flex flex-col">
                
                <!-- Alertas Flash Globales -->
                @if(session('success'))
                    <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-4 mb-8 rounded-2xl flex items-center space-x-3 shadow-sm">
                        <i class="fas fa-check-circle text-emerald-500 text-lg"></i>
                        <p class="font-bold text-sm">{{ session('success') }}</p>
                    </div>
                @endif
                @if(session('error'))
                    <div class="bg-rose-50 border border-rose-200 text-rose-700 px-6 py-4 mb-8 rounded-2xl flex items-center space-x-3 shadow-sm">
                        <i class="fas fa-exclamation-circle text-rose-500 text-lg"></i>
                        <p class="font-bold text-sm">{{ session('error') }}</p>
                    </div>
                @endif

                <!-- Título o Cabecera de Página -->
                <div class="mb-8">
                    @yield('header')
                </div>

                <!-- Contenido Principal Dinámico -->
                <div class="flex-1">
                    @yield('content')
                </div>
                
            </div>
        </main>
    </div>

    <!-- Lógica del Menú Móvil -->
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            
            if (sidebar.classList.contains('-translate-x-full')) {
                // Abrir
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
                setTimeout(() => overlay.classList.remove('opacity-0'), 10);
            } else {
                // Cerrar
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('opacity-0');
                setTimeout(() => overlay.classList.add('hidden'), 300);
            }
        }
    </script>

    @yield('scripts')
</body>
</html>
