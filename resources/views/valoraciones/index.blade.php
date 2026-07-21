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
            Control de calidad del servicio y retroalimentación de los clientes.
        </p>
    </div>
    <div class="relative inline-block text-left" x-data="{ open: false }">
        <button @click="open = !open" type="button" class="bg-stone-800 hover:bg-stone-900 text-white px-5 py-2.5 rounded-xl font-bold shadow-md transition-all flex items-center gap-2 text-xs">
            <i class="fas fa-file-export text-amber-400"></i>
            <span>Exportar</span>
            <i class="fas fa-chevron-down text-xs"></i>
        </button>
        <div x-show="open" @click.away="open = false" class="origin-top-right absolute right-0 mt-2 w-48 rounded-2xl shadow-xl bg-white ring-1 ring-black ring-opacity-5 z-50 divide-y divide-gray-100 p-1">
            <a href="{{ route('reports.export', ['modulo' => 'valoraciones', 'format' => 'excel']) }}" class="flex items-center space-x-2 px-4 py-2.5 text-xs font-bold text-emerald-700 hover:bg-emerald-50 rounded-xl transition-colors">
                <i class="fas fa-file-excel text-emerald-500 text-sm"></i>
                <span>Exportar Excel (.csv)</span>
            </a>
            <a href="{{ route('reports.export', ['modulo' => 'valoraciones', 'format' => 'pdf']) }}" class="flex items-center space-x-2 px-4 py-2.5 text-xs font-bold text-rose-700 hover:bg-rose-50 rounded-xl transition-colors">
                <i class="fas fa-file-pdf text-rose-500 text-sm"></i>
                <span>Reporte PDF / Imprimir</span>
            </a>
        </div>
    </div>
</div>
@endsection

@section('content')
<!-- Formulario de Valoración para Clientes / Servicios Atendidos -->
<div class="bg-gradient-to-r from-rose-500 to-amber-500 p-0.5 rounded-3xl shadow-md mb-8">
    <div class="bg-white p-6 sm:p-8 rounded-[23px]">
        <div class="flex items-center space-x-3 mb-6">
            <div class="w-10 h-10 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center font-bold text-lg shadow-sm">
                <i class="fas fa-star"></i>
            </div>
            <div>
                <h2 class="text-lg font-black text-gray-900 tracking-tight">Dejar tu Calificación y Opinión</h2>
                <p class="text-xs text-gray-500 font-medium">Evalúa la atención y calidad del servicio recibido</p>
            </div>
        </div>

        @if(isset($citasPendientes) && $citasPendientes->count() > 0)
            <form action="{{ route('valoraciones.store') }}" method="POST" class="space-y-5">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-extrabold uppercase text-gray-700 mb-2">Selecciona el Servicio Realizado</label>
                        <select name="cita_id" required class="w-full bg-gray-50 border border-gray-200 text-xs font-bold rounded-2xl p-3.5 text-gray-800 focus:ring-2 focus:ring-rose-500 focus:border-rose-500 transition-all cursor-pointer">
                            @foreach($citasPendientes as $cita)
                                <option value="{{ $cita->id }}">
                                    {{ $cita->servicio->nombre ?? 'Servicio' }} - Atendido por {{ $cita->estilista->name ?? 'Estilista' }} ({{ \Carbon\Carbon::parse($cita->fecha)->format('d/m/Y') }})
                                    @if(auth()->user()->hasRole('administrador') || auth()->user()->hasRole('recepcionista'))
                                        [Cliente: {{ $cita->cliente->name ?? 'Cliente' }}]
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-extrabold uppercase text-gray-700 mb-2">Calificación (1 a 5 Estrellas)</label>
                        <div class="flex items-center justify-between bg-gray-50 p-2.5 px-4 rounded-2xl border border-gray-200">
                            <input type="hidden" name="estrellas" id="estrellas_input" value="5" required>
                            <div class="flex items-center space-x-1 cursor-pointer text-amber-400 text-2xl" id="star_rating_container">
                                <i class="fas fa-star star-btn transition-transform duration-150" data-value="1"></i>
                                <i class="fas fa-star star-btn transition-transform duration-150" data-value="2"></i>
                                <i class="fas fa-star star-btn transition-transform duration-150" data-value="3"></i>
                                <i class="fas fa-star star-btn transition-transform duration-150" data-value="4"></i>
                                <i class="fas fa-star star-btn transition-transform duration-150" data-value="5"></i>
                            </div>
                            <span class="text-xs font-black text-amber-600" id="star_label">5.0 / Excelente</span>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-extrabold uppercase text-gray-700 mb-2">Tu Comentario u Opinión (Opcional)</label>
                    <textarea name="comentario" rows="3" placeholder="¿Qué te pareció la atención, la puntualidad y el resultado de tu servicio? Cuéntanos tu opinión..." class="w-full bg-gray-50 border border-gray-200 text-xs font-medium rounded-2xl p-3.5 text-gray-800 focus:ring-2 focus:ring-rose-500 focus:border-rose-500 transition-all"></textarea>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="bg-gradient-to-r from-rose-500 to-rose-600 hover:from-rose-600 hover:to-rose-700 text-white font-extrabold px-6 py-3.5 rounded-2xl text-xs shadow-md shadow-rose-200 transition-all flex items-center space-x-2 transform hover:scale-[1.02] active:scale-95">
                        <i class="fas fa-paper-plane"></i>
                        <span>Enviar Valoración</span>
                    </button>
                </div>
            </form>
        @else
            <div class="p-4 bg-rose-50/60 border border-rose-100 rounded-2xl flex items-center space-x-3 text-rose-700">
                <i class="fas fa-info-circle text-lg shrink-0 text-rose-500"></i>
                <p class="text-xs font-semibold">
                    @if(auth()->user()->hasRole('cliente'))
                        No tienes citas completadas pendientes de calificar. Tan pronto asistas a tu cita y el servicio sea completado, podrás evaluarlo desde aquí.
                    @else
                        No hay servicios completados pendientes de valorar en este momento.
                    @endif
                </p>
            </div>
        @endif
    </div>
</div>

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

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const stars = document.querySelectorAll('.star-btn');
        const input = document.getElementById('estrellas_input');
        const label = document.getElementById('star_label');

        if (!stars.length || !input || !label) return;

        const labels = {
            1: '1.0 / Deficiente',
            2: '2.0 / Regular',
            3: '3.0 / Bueno',
            4: '4.0 / Muy Bueno',
            5: '5.0 / Excelente'
        };

        stars.forEach(star => {
            star.addEventListener('click', function() {
                const val = parseInt(this.getAttribute('data-value'));
                input.value = val;
                label.textContent = labels[val] || (val + '.0');
                
                stars.forEach((s, idx) => {
                    if (idx < val) {
                        s.classList.remove('text-gray-300');
                        s.classList.add('text-amber-400');
                    } else {
                        s.classList.remove('text-amber-400');
                        s.classList.add('text-gray-300');
                    }
                });
            });

            star.addEventListener('mouseenter', function() {
                const val = parseInt(this.getAttribute('data-value'));
                stars.forEach((s, idx) => {
                    if (idx < val) {
                        s.classList.add('scale-125');
                    }
                });
            });

            star.addEventListener('mouseleave', function() {
                stars.forEach(s => s.classList.remove('scale-125'));
            });
        });
    });
</script>
@endsection
