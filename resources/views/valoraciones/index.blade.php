@extends('layouts.app')

@section('title', 'Valoraciones y Satisfacción - Salón Anita')

@section('header')
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-black text-gray-900 tracking-tight flex items-center gap-3">
            <i class="fas fa-star text-amber-400"></i>
            Encuestas de Satisfacción y Opiniones (NPS)
        </h1>
        <p class="text-xs font-semibold text-gray-500 mt-1">
            Control de calidad del servicio y retroalimentación de los clientes (Opción 5)
        </p>
    </div>
</div>
@endsection

@section('content')
<!-- Resumen de Satisfacción -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-gradient-to-br from-amber-500 to-amber-600 text-white p-6 rounded-3xl shadow-lg relative overflow-hidden">
        <span class="text-xs font-extrabold uppercase tracking-widest text-amber-100">Promedio General</span>
        <div class="flex items-baseline space-x-2 mt-2">
            <h3 class="text-4xl font-black">{{ number_format($promedioGlobal, 1) }}</h3>
            <span class="text-lg font-bold text-amber-200">/ 5.0</span>
        </div>
        <div class="flex items-center space-x-1 mt-2 text-amber-200">
            @for($i=1; $i<=5; $i++)
                <i class="fas fa-star {{ $i <= round($promedioGlobal) ? 'text-amber-100' : 'text-amber-400/50' }}"></i>
            @endfor
        </div>
    </div>

    <!-- Rendimiento por Estilista -->
    <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm col-span-2">
        <h3 class="text-xs font-extrabold uppercase tracking-wider text-gray-400 mb-4">Calificación Promedio por Estilista</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @forelse($rendimientoEstilistas as $estilista)
            <div class="p-3.5 bg-gray-50 rounded-2xl border border-gray-100 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-9 h-9 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center font-bold text-xs">
                        {{ strtoupper(substr($estilista->name, 0, 1)) }}
                    </div>
                    <div>
                        <h4 class="text-xs font-extrabold text-gray-900">{{ $estilista->name }}</h4>
                        <p class="text-[10px] text-gray-400 font-semibold">{{ $estilista->total_opiniones }} valoraciones</p>
                    </div>
                </div>
                <div class="text-right">
                    <span class="text-sm font-black text-amber-500 flex items-center gap-1">
                        <i class="fas fa-star"></i>
                        {{ number_format($estilista->promedio_estrellas ?: 0, 1) }}
                    </span>
                </div>
            </div>
            @empty
            <p class="text-xs text-gray-400 col-span-2">No hay datos de estilistas.</p>
            @endforelse
        </div>
    </div>
</div>

<!-- Listado de Opiniones de Clientes -->
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="p-6 border-b border-gray-100 bg-gray-50/50">
        <h2 class="text-base font-extrabold text-gray-900">Últimas Calificaciones y Comentarios</h2>
    </div>
    <div class="divide-y divide-gray-100">
        @forelse($valoraciones as $val)
        <div class="p-6 hover:bg-gray-50/50 transition-colors">
            <div class="flex items-start justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-sm">
                        {{ strtoupper(substr($val->cliente->name ?? 'C', 0, 1)) }}
                    </div>
                    <div>
                        <h3 class="text-sm font-extrabold text-gray-900">{{ $val->cliente->name ?? 'Cliente' }}</h3>
                        <p class="text-xs text-gray-400 font-medium">
                            Atendido por <span class="font-bold text-gray-700">{{ $val->estilista->name ?? 'Estilista' }}</span> 
                            en servicio de <span class="font-bold text-gray-700">{{ $val->cita->servicio->nombre ?? 'Servicio' }}</span>
                        </p>
                    </div>
                </div>
                <div class="text-right">
                    <div class="flex items-center space-x-1 text-amber-400">
                        @for($i=1; $i<=5; $i++)
                            <i class="fas fa-star text-xs {{ $i <= $val->estrellas ? 'text-amber-400' : 'text-gray-200' }}"></i>
                        @endfor
                    </div>
                    <span class="text-[10px] text-gray-400 font-semibold mt-1 block">{{ $val->fecha->format('d/m/Y H:i') }}</span>
                </div>
            </div>
            @if($val->comentario)
            <div class="mt-3 bg-gray-50 p-3 rounded-xl border border-gray-100">
                <p class="text-xs font-semibold text-gray-700 italic">"{{ $val->comentario }}"</p>
            </div>
            @endif
        </div>
        @empty
        <div class="p-8 text-center text-gray-400 font-medium text-xs">
            No se han registrado valoraciones aún.
        </div>
        @endforelse
    </div>
    <div class="p-4 border-t border-gray-100">
        {{ $valoraciones->links() }}
    </div>
</div>
@endsection
