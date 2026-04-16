@extends('layouts.app')

@section('title', 'Dashboard - Salón de Belleza Anita')

@section('header')
<div class="flex justify-between items-center mb-8">
    <div>
        <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Inicio</h1>
        <p class="text-gray-500 text-sm">Resumen general del día.</p>
    </div>
    <div class="bg-white px-4 py-2 rounded-lg border border-gray-200 shadow-sm">
        <span class="text-gray-600 font-bold text-xs uppercase tracking-widest">{{ now()->format('d M, Y') }}</span>
    </div>
</div>
@endsection

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 @if(auth()->user()->hasPermission('manage_inventory')) lg:grid-cols-3 xl:grid-cols-6 @else lg:grid-cols-2 @endif gap-4 mb-8">
    <!-- Bienvenida (Visible para todos) -->
    <div class="bg-gradient-to-br from-rose-500 to-rose-600 p-6 rounded-3xl text-white shadow-lg shadow-rose-100 flex items-center justify-between col-span-1 md:col-span-2 @if(!auth()->user()->hasPermission('manage_inventory')) lg:col-span-2 @endif">
        <div>
            <h3 class="text-xl font-bold mb-1">¡Hola, {{ explode(' ', auth()->user()->name ?? auth()->user()->email)[0] }}!</h3>
            <p class="text-rose-100 text-xs font-medium"> @if(auth()->user()->hasRole('administrador')) Tienes control total del sistema. @else Explora nuestros productos y agenda tu cita. @endif </p>
        </div>
        <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center">
            <i class="fas fa-sparkles text-xl"></i>
        </div>
    </div>

    @if(auth()->user()->hasPermission('manage_inventory'))
        <!-- Ventas (Admin) -->
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-dollar-sign text-xl"></i>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-none mb-1">Ventas</p>
                    <h3 class="text-2xl font-bold text-gray-800 leading-none">Bs. {{ number_format($stats['total_sales'], 2) }}</h3>
                </div>
            </div>
        </div>

        <!-- Promotores (Admin) -->
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-violet-100 text-violet-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-user-tie text-xl"></i>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-none mb-1">Promotores</p>
                    <h3 class="text-2xl font-bold text-gray-800 leading-none">{{ $stats['promotores_count'] }}</h3>
                </div>
            </div>
        </div>
    @endif

    <!-- Productos (Visible para todos, pero con distinto texto) -->
    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition">
        <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-amber-100 text-amber-600 rounded-xl flex items-center justify-center">
                <i class="fas fa-shopping-bag text-xl"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-none mb-1">@if(auth()->user()->hasPermission('manage_inventory')) Inventario @else Productos @endif</p>
                <h3 class="text-2xl font-bold text-gray-800 leading-none">{{ $stats['productos_count'] }}</h3>
            </div>
        </div>
    </div>

    @if(auth()->user()->hasPermission('manage_users'))
        <!-- Personal (Admin) -->
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-users text-xl"></i>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-none mb-1">Personal</p>
                    <h3 class="text-2xl font-bold text-gray-800 leading-none">{{ $stats['users_count'] }}</h3>
                </div>
            </div>
        </div>
    @endif

    @if(auth()->user()->hasPermission('view_audit_log'))
        <!-- Auditoria (Admin) -->
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-gray-100 text-gray-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-history text-xl"></i>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-none mb-1">Eventos</p>
                    <h3 class="text-2xl font-bold text-gray-800 leading-none">{{ $stats['logs_count'] }}</h3>
                </div>
            </div>
        </div>
    @endif
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    @if(auth()->user()->hasPermission('view_audit_log'))
    <div class="bg-white p-8 rounded-2xl border border-gray-200 shadow-sm">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-bold text-gray-800">Actividad Reciente</h2>
            <a href="{{ route('activity_logs.index') }}" class="text-rose-500 text-xs font-bold uppercase hover:underline">Ver todo</a>
        </div>
        <div class="space-y-4">
            @forelse($recent_logs as $log)
                <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-xl transition">
                    <div class="flex items-center space-x-4">
                        <div class="w-2 h-2 rounded-full 
                            @if($log->action === 'CREATE' || $log->action === 'REGISTER') bg-emerald-400
                            @elseif($log->action === 'DELETE') bg-rose-400
                            @elseif($log->action === 'LOGIN') bg-blue-400
                            @else bg-orange-400 @endif">
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-700">{{ $log->description }}</p>
                            <p class="text-[10px] text-gray-400 uppercase font-bold">
                                {{ $log->created_at->diffForHumans() }} 
                                <span class="mx-1 text-gray-200">•</span>
                                <span class="text-gray-300">{{ $log->created_at->format('d/m/Y h:i A') }}</span>
                            </p>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-center text-gray-400 py-10 text-sm italic">Sin movimientos recientes</p>
            @endforelse
        </div>
    </div>
    @else
    <div class="bg-white p-8 rounded-2xl border border-gray-200 shadow-sm">
        <h2 class="text-lg font-bold text-gray-800 mb-6">Novedades para ti</h2>
        <div class="space-y-4">
            <div class="p-6 bg-rose-50 rounded-2xl border border-rose-100 border-dashed">
                <i class="fas fa-gift text-rose-500 text-3xl mb-4"></i>
                <h4 class="font-bold text-gray-800">Próximamente: Sistema de Puntos</h4>
                <p class="text-gray-500 text-sm mt-1">Gana puntos por cada compra y servicios realizados para canjear por premios.</p>
            </div>
            <div class="p-6 bg-rose-50 rounded-2xl border border-rose-100 border-dashed">
                <i class="fas fa-calendar-star text-rose-500 text-3xl mb-4"></i>
                <h4 class="font-bold text-gray-800">Agenda Online en Camino</h4>
                <p class="text-gray-500 text-sm mt-1">Estamos trabajando para que puedas reservar tu cita favorita desde aquí.</p>
            </div>
        </div>
    </div>
    @endif

    <div class="bg-white p-8 rounded-2xl border border-gray-200 shadow-sm flex flex-col">
        <h2 class="text-lg font-bold text-gray-800 mb-6 font-bold">Catálogo de Productos</h2>
        <div class="flex-1 flex flex-col items-center justify-center py-10 text-center border-2 border-dashed border-gray-100 rounded-2xl">
            <i class="fas fa-shopping-bag text-gray-200 text-6xl mb-4"></i>
            <p class="text-gray-400 text-sm mb-6 max-w-[200px]">Echa un vistazo a lo que tenemos disponible para tu cuidado personal.</p>
            <a href="{{ route('productos.index') }}" class="bg-rose-500 text-white px-8 py-3 rounded-xl font-bold shadow-lg shadow-rose-100 hover:scale-105 transition-all">
                Ver Catálogo
            </a>
        </div>
    </div>
</div>
@endsection
