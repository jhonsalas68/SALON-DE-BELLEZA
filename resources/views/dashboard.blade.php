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
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <!-- Citas -->
    <div class="bg-white p-6 rounded-2xl border border-gray-200">
        <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-rose-100 text-rose-600 rounded-xl flex items-center justify-center">
                <i class="fas fa-calendar-check text-xl"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-none mb-1">Citas hoy</p>
                <h3 class="text-2xl font-bold text-gray-800 leading-none">{{ $stats['appointments_today'] }}</h3>
            </div>
        </div>
    </div>

    <!-- Ventas -->
    <div class="bg-white p-6 rounded-2xl border border-gray-200">
        <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-orange-100 text-orange-600 rounded-xl flex items-center justify-center">
                <i class="fas fa-dollar-sign text-xl"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-none mb-1">Ventas</p>
                <h3 class="text-2xl font-bold text-gray-800 leading-none">${{ number_format($stats['total_sales'], 2) }}</h3>
            </div>
        </div>
    </div>

    <!-- Usuarios -->
    <div class="bg-white p-6 rounded-2xl border border-gray-200">
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

    <!-- Auditoria -->
    <div class="bg-white p-6 rounded-2xl border border-gray-200">
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
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
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
                            <p class="text-[10px] text-gray-400 uppercase font-bold">{{ $log->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-center text-gray-400 py-10 text-sm italic">Sin movimientos recientes</p>
            @endforelse
        </div>
    </div>

    <div class="bg-white p-8 rounded-2xl border border-gray-200 shadow-sm">
        <h2 class="text-lg font-bold text-gray-800 mb-6">Próximos Servicios</h2>
        <div class="py-10 text-center border-2 border-dashed border-gray-100 rounded-2xl">
            <p class="text-gray-400 text-sm">Próximamente estaremos listos para agendar servicios.</p>
        </div>
    </div>
</div>
@endsection
