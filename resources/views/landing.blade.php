@extends('layouts.guest')

@section('title', 'Salón de Belleza Anita - Estilo y Bienestar Premium')

@section('content')
    <!-- Hero Section -->
    <section id="inicio" class="hero-gradient pt-36 pb-20 px-6">
        <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            <!-- Text Content -->
            <div class="lg:col-span-7 text-center lg:text-left space-y-6">
                <div class="inline-flex items-center space-x-2 bg-rose-100/50 border border-rose-200/50 px-3 py-1.5 rounded-full">
                    <span class="w-2 h-2 rounded-full bg-rose-500 animate-ping"></span>
                    <span class="text-xs font-bold text-rose-700 uppercase tracking-widest">Reserva y Compra Online</span>
                </div>
                <h2 class="serif text-5xl lg:text-7xl font-light text-stone-900 leading-tight">
                    Resalta tu <span class="font-normal italic text-amber-600">belleza</span> con nuestro toque profesional.
                </h2>
                <p class="text-lg text-stone-600 max-w-2xl leading-relaxed">
                    Experimenta el cuidado capilar y estético premium en Salón Anita. Agenda citas en segundos con tus estilistas favoritos y adquiere productos exclusivos con envío directo.
                </p>
                <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4">
                    <a href="#servicios" class="w-full sm:w-auto text-center bg-stone-900 hover:bg-amber-600 text-white font-extrabold px-8 py-4 rounded-2xl transition-all shadow-lg shadow-stone-200">
                        <i class="far fa-calendar-alt mr-2"></i> Agendar Cita
                    </a>
                    <a href="#productos" class="w-full sm:w-auto text-center bg-white hover:bg-stone-50 border border-stone-200 text-stone-700 font-extrabold px-8 py-4 rounded-2xl transition-all shadow-sm">
                        <i class="fas fa-shopping-bag mr-2"></i> Ver Catálogo
                    </a>
                </div>
            </div>

            <!-- Image Mockup -->
            <div class="lg:col-span-5 flex justify-center relative">
                <div class="absolute -inset-2 bg-gradient-to-tr from-amber-500 to-rose-400 rounded-[2.5rem] blur-2xl opacity-20"></div>
                <div class="relative bg-white rounded-[2.5rem] p-4 shadow-xl border border-stone-100 max-w-sm">
                    <!-- Premium visual presentation using gradients and icon -->
                    <div class="w-full aspect-[4/5] rounded-[2rem] bg-gradient-to-br from-amber-100 via-rose-100 to-amber-200 flex flex-col justify-between p-8 overflow-hidden relative">
                        <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-rose-300/30 rounded-full blur-2xl"></div>
                        <div class="absolute -left-10 -top-10 w-48 h-48 bg-amber-300/30 rounded-full blur-2xl"></div>
                        
                        <div class="flex justify-between items-start">
                            <span class="bg-white/80 backdrop-blur-md px-3.5 py-1.5 rounded-full text-xs font-black uppercase text-amber-700">Premium Care</span>
                            <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center text-rose-500 shadow-md">
                                <i class="fas fa-heart"></i>
                            </div>
                        </div>

                        <div class="space-y-4 relative z-10">
                            <p class="serif text-3xl font-light text-stone-800 leading-tight">
                                Cabello <span class="font-semibold italic">sano</span>, estilo único.
                            </p>
                            <div class="h-1.5 w-16 bg-amber-500 rounded-full"></div>
                            <p class="text-xs text-stone-500 leading-relaxed">
                                Ofrecemos cortes, tinte, tratamientos capilares avanzados y peinados de gala adaptados a tu personalidad.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Sección Promociones -->
    @if(!$promociones->isEmpty())
    <section id="promociones" class="py-20 bg-stone-100/50 border-y border-stone-200/30 px-6">
        <div class="max-w-7xl mx-auto">
            <div class="text-center space-y-2 mb-12">
                <span class="text-xs font-black uppercase tracking-widest text-amber-600">Descuentos Especiales</span>
                <h3 class="serif text-3xl lg:text-4xl text-stone-900">Promociones Activas de la Semana</h3>
                <div class="h-1 w-12 bg-amber-500 mx-auto rounded-full mt-4"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($promociones as $promo)
                    <div class="bg-white rounded-3xl p-6 shadow-sm border border-stone-200/50 hover:shadow-lg transition-all relative overflow-hidden group">
                        <div class="absolute right-0 top-0 bg-rose-500 text-white font-black text-xs px-4 py-2 rounded-bl-2xl">
                            -{{ number_format($promo->descuento_porcentaje, 0) }}%
                        </div>
                        <div class="space-y-4">
                            <span class="inline-block px-3 py-1 bg-amber-50 border border-amber-200/50 text-[10px] font-black uppercase tracking-widest text-amber-700 rounded-full">
                                {{ $promo->servicio_id ? 'Servicio' : 'Producto' }}
                            </span>
                            <h4 class="text-lg font-extrabold text-stone-900">{{ $promo->nombre }}</h4>
                            <p class="text-sm text-stone-500 leading-relaxed">{{ $promo->descripcion }}</p>
                            
                            <div class="border-t border-stone-100 pt-4 flex items-center justify-between text-xs text-stone-400 font-bold">
                                <span><i class="far fa-calendar-alt mr-1"></i> Fin: {{ \Carbon\Carbon::parse($promo->fecha_fin)->format('d/m/Y') }}</span>
                                <a href="{{ $promo->servicio_id ? '#servicios' : '#productos' }}" class="text-amber-600 hover:text-amber-700 font-extrabold flex items-center space-x-1">
                                    <span>Ver catálogo</span>
                                    <i class="fas fa-arrow-right text-[10px] group-hover:translate-x-1 transition-transform"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Sección Servicios (Reservas) -->
    <section id="servicios" class="py-20 px-6">
        <div class="max-w-7xl mx-auto">
            <div class="text-center space-y-2 mb-16">
                <span class="text-xs font-black uppercase tracking-widest text-rose-500">Nuestros Servicios</span>
                <h3 class="serif text-3xl lg:text-4xl text-stone-900">Agenda tu Sesión</h3>
                <p class="text-sm text-stone-500 max-w-lg mx-auto">Selecciona tu servicio predilecto, elige a tu estilista de confianza y selecciona el horario de tu preferencia.</p>
                <div class="h-1 w-12 bg-rose-500 mx-auto rounded-full mt-4"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($servicios as $servicio)
                    @php
                        // Buscar si el servicio tiene una promoción activa
                        $promo = $promociones->where('servicio_id', $servicio->id)->first();
                        $precioOriginal = $servicio->precio;
                        $precioFinal = $precioOriginal;
                        if ($promo) {
                            $precioFinal = $precioOriginal - ($precioOriginal * $promo->descuento_porcentaje) / 100;
                        }
                    @endphp
                    <div class="bg-white rounded-3xl p-6 shadow-sm border border-stone-200/50 hover:shadow-lg transition-all flex flex-col justify-between">
                        <div class="space-y-3">
                            <div class="flex justify-between items-start">
                                <h4 class="text-lg font-extrabold text-stone-900">{{ $servicio->nombre }}</h4>
                                <span class="text-xs font-bold text-stone-400 shrink-0 bg-stone-50 px-2.5 py-1 rounded-lg border border-stone-100 flex items-center">
                                    <i class="far fa-clock mr-1 text-rose-400"></i> {{ $servicio->duracion_minutos }} min
                                </span>
                            </div>
                            <p class="text-sm text-stone-500 leading-relaxed">Disfruta de una atención personalizada con productos importados de la más alta calidad.</p>
                        </div>

                        <div class="mt-6 border-t border-stone-100 pt-6 flex items-center justify-between">
                            <div>
                                @if($promo)
                                    <span class="text-xs text-stone-400 line-through font-bold">Bs {{ number_format($precioOriginal, 2) }}</span>
                                    <span class="block text-xl font-black text-rose-500">Bs {{ number_format($precioFinal, 2) }}</span>
                                @else
                                    <span class="block text-xl font-black text-stone-900">Bs {{ number_format($precioOriginal, 2) }}</span>
                                @endif
                            </div>

                            @auth
                                <button onclick="openBookingModal('{{ $servicio->id }}', '{{ $servicio->nombre }}')" class="bg-stone-900 hover:bg-rose-500 text-white font-extrabold px-5 py-2.5 rounded-xl transition-all text-xs flex items-center space-x-1.5 shadow-sm">
                                    <i class="far fa-calendar-alt"></i>
                                    <span>Reservar</span>
                                </button>
                            @else
                                <a href="{{ route('login') }}" class="bg-stone-900 hover:bg-amber-600 text-white font-extrabold px-5 py-2.5 rounded-xl transition-all text-xs flex items-center space-x-1.5 shadow-sm">
                                    <i class="fas fa-sign-in-alt"></i>
                                    <span>Ingresa para Reservar</span>
                                </a>
                            @endauth
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Sección Tienda (Productos) -->
    <section id="productos" class="py-20 bg-stone-100/50 border-t border-stone-200/30 px-6">
        <div class="max-w-7xl mx-auto">
            <div class="text-center space-y-2 mb-16">
                <span class="text-xs font-black uppercase tracking-widest text-amber-600">Catálogo de Productos</span>
                <h3 class="serif text-3xl lg:text-4xl text-stone-900">Cuidado Capilar Premium en Casa</h3>
                <p class="text-sm text-stone-500 max-w-lg mx-auto">Compra de forma 100% segura con tarjeta de crédito/débito a través de Stripe y recógelo o pídelo a domicilio.</p>
                <div class="h-1 w-12 bg-amber-500 mx-auto rounded-full mt-4"></div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                @foreach($productos as $producto)
                    @php
                        $promo = $promociones->where('producto_id', $producto->id)->first();
                        $precioOriginal = $producto->precio_venta;
                        $precioFinal = $precioOriginal;
                        if ($promo) {
                            $precioFinal = $precioOriginal - ($precioOriginal * $promo->descuento_porcentaje) / 100;
                        }
                    @endphp
                    <div class="bg-white rounded-3xl p-5 shadow-sm border border-stone-200/50 hover:shadow-lg transition-all flex flex-col justify-between">
                        <div>
                            @php
                                $nombreLower = strtolower($producto->nombre);
                                $icon = 'fa-box-open';
                                $gradient = 'from-stone-50 to-stone-100 text-stone-500';
                                if (str_contains($nombreLower, 'shampoo') || str_contains($nombreLower, 'champú')) {
                                    $icon = 'fa-pump-soap';
                                    $gradient = 'from-emerald-50 to-teal-50 text-teal-600';
                                } elseif (str_contains($nombreLower, 'acondicionador')) {
                                    $icon = 'fa-pump-soap';
                                    $gradient = 'from-sky-50 to-blue-50 text-blue-600';
                                } elseif (str_contains($nombreLower, 'tinte') || str_contains($nombreLower, 'color')) {
                                    $icon = 'fa-paint-brush';
                                    $gradient = 'from-purple-50 to-pink-50 text-pink-600';
                                } elseif (str_contains($nombreLower, 'laca') || str_contains($nombreLower, 'spray')) {
                                    $icon = 'fa-spray-can';
                                    $gradient = 'from-amber-50 to-orange-50 text-orange-600';
                                } elseif (str_contains($nombreLower, 'crema')) {
                                    $icon = 'fa-jar';
                                    $gradient = 'from-rose-50 to-red-50 text-rose-600';
                                }
                            @endphp
                            <div class="w-full aspect-square rounded-2xl bg-gradient-to-br {{ $gradient }} border border-stone-150 flex items-center justify-center mb-4 relative overflow-hidden group">
                                @if($producto->imagen)
                                    <img src="{{ Str::startsWith($producto->imagen, 'http') ? $producto->imagen : asset($producto->imagen) }}" alt="{{ $producto->nombre }}" class="w-full h-full object-cover rounded-2xl group-hover:scale-105 transition-transform duration-300">
                                @else
                                    <div class="absolute -right-6 -bottom-6 w-20 h-20 bg-white/30 rounded-full blur-xl transition-all group-hover:scale-125"></div>
                                    <i class="fas {{ $icon }} text-5xl group-hover:scale-110 transition-transform duration-300"></i>
                                @endif
                                @if($producto->stock <= $producto->stock_minimo)
                                    <span class="absolute left-2.5 top-2.5 bg-amber-500 text-white font-extrabold text-[9px] px-2 py-0.5 rounded-md animate-pulse">¡Stock Bajo!</span>
                                @endif
                            </div>
                            <div class="space-y-1">
                                <div class="flex justify-between items-center text-[10px] text-stone-400 font-bold">
                                    <span>CÓD: {{ $producto->codigo }}</span>
                                    <span>Stock: {{ $producto->stock }}</span>
                                </div>
                                <h4 class="font-extrabold text-stone-900 text-sm line-clamp-2 h-10">{{ $producto->nombre }}</h4>
                            </div>
                        </div>

                        <div class="mt-4 pt-4 border-t border-stone-100">
                            <div class="flex justify-between items-end mb-4">
                                <div>
                                    @if($promo)
                                        <span class="text-xs text-stone-400 line-through font-bold">Bs {{ number_format($precioOriginal, 2) }}</span>
                                        <span class="block text-lg font-black text-rose-500">Bs {{ number_format($precioFinal, 2) }}</span>
                                    @else
                                        <span class="block text-lg font-black text-stone-900">Bs {{ number_format($precioOriginal, 2) }}</span>
                                    @endif
                                </div>
                            </div>

                            @auth
                                <form action="{{ route('client.products.buy') }}" method="POST" class="space-y-3">
                                    @csrf
                                    <input type="hidden" name="producto_id" value="{{ $producto->id }}">
                                    
                                    <div class="flex items-center justify-between bg-stone-50 border border-stone-200/60 rounded-xl px-2 py-1">
                                        <span class="text-[10px] font-black text-stone-400 uppercase tracking-widest pl-1">Cantidad</span>
                                        <div class="flex items-center space-x-2">
                                            <button type="button" onclick="decrementQty(this)" class="w-6 h-6 rounded-md bg-white border border-stone-200/50 flex items-center justify-center text-xs font-bold text-stone-600 hover:bg-stone-100 transition-colors">-</button>
                                            <input type="number" name="cantidad" value="1" min="1" max="{{ $producto->stock }}" readonly class="w-8 text-center text-xs font-black bg-transparent border-none focus:outline-none select-none">
                                            <button type="button" onclick="incrementQty(this, {{ $producto->stock }})" class="w-6 h-6 rounded-md bg-white border border-stone-200/50 flex items-center justify-center text-xs font-bold text-stone-600 hover:bg-stone-100 transition-colors">+</button>
                                        </div>
                                    </div>

                                    <button type="submit" class="w-full bg-stone-900 hover:bg-amber-600 text-white font-extrabold py-3 px-4 rounded-xl transition-all text-xs flex items-center justify-center space-x-1.5 shadow-sm">
                                        <i class="fab fa-stripe text-lg"></i>
                                        <span>Comprar con Stripe</span>
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('login') }}" class="w-full bg-stone-950 hover:bg-amber-600 text-white font-extrabold py-3 px-4 rounded-xl transition-all text-xs flex items-center justify-center space-x-1.5 shadow-sm">
                                    <i class="fas fa-sign-in-alt"></i>
                                    <span>Ingresa para Comprar</span>
                                </a>
                            @endauth
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    @auth
        <section id="portal" class="py-20 px-6 bg-stone-50">
            <div class="max-w-7xl mx-auto space-y-12">
                <!-- Header Portal -->
                <div class="text-center space-y-2">
                    <span class="text-xs font-black uppercase tracking-widest text-amber-600">Mi Cuenta Cliente</span>
                    <h3 class="serif text-3xl lg:text-4xl text-stone-900">Tu Historial y Puntos de Fidelidad</h3>
                    <div class="h-1 w-12 bg-amber-500 mx-auto rounded-full mt-4"></div>
                </div>

                <!-- Tarjeta Principal de Programa de Puntos y Fidelización -->
                <div class="bg-gradient-to-r from-amber-500 via-amber-600 to-rose-500 rounded-3xl p-8 text-white shadow-xl relative overflow-hidden">
                    <div class="absolute right-0 top-0 -mr-10 -mt-10 w-48 h-48 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
                    <div class="relative z-10 grid grid-cols-1 md:grid-cols-3 gap-6 items-center">
                        <div class="md:col-span-2 space-y-3">
                            <div class="inline-flex items-center space-x-2 bg-white/20 backdrop-blur-md px-3 py-1 rounded-full text-xs font-black text-amber-100 uppercase tracking-widest">
                                <i class="fas fa-gem"></i>
                                <span>Programa de Fidelidad Anita</span>
                            </div>
                            <h4 class="serif text-3xl font-light">Acumula puntos en cada servicio</h4>
                            <p class="text-xs text-amber-100 font-medium leading-relaxed max-w-xl">
                                ¡Ganas <strong class="text-white font-extrabold">1 punto por cada 10 Bs</strong> consumidos en citas completadas y compras de productos! Tus puntos reconocen tu preferencia y te permiten acceder a promociones exclusivas.
                            </p>
                        </div>

                        <div class="bg-white/15 backdrop-blur-md p-6 rounded-2xl border border-white/20 text-center space-y-1 shadow-inner">
                            <span class="text-xs font-extrabold uppercase tracking-widest text-amber-100">Tus Puntos Disponibles</span>
                            <div class="flex items-center justify-center space-x-2">
                                <i class="fas fa-gem text-amber-300 text-3xl animate-pulse"></i>
                                <span class="text-5xl font-black text-white">{{ auth()->user()->puntos ?? 0 }}</span>
                            </div>
                            <span class="text-[10px] text-amber-200 font-bold block uppercase tracking-wider">Puntos Acumulados</span>
                        </div>
                    </div>

                    @if(isset($puntosHistorial) && !$puntosHistorial->isEmpty())
                        <div class="mt-6 pt-6 border-t border-white/20">
                            <p class="text-xs font-extrabold uppercase tracking-wider text-amber-100 mb-3"><i class="fas fa-history mr-1"></i> Historial Reciente de Puntos</p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                                @foreach($puntosHistorial as $ph)
                                    <div class="bg-white/10 backdrop-blur-md p-3 rounded-xl border border-white/10 flex items-center justify-between text-xs">
                                        <div class="truncate pr-2">
                                            <p class="font-bold text-white truncate">{{ $ph->descripcion }}</p>
                                            <span class="text-[9px] text-amber-200">{{ \Carbon\Carbon::parse($ph->created_at)->format('d/m/Y H:i') }}</span>
                                        </div>
                                        <span class="font-black px-2 py-0.5 rounded-md text-xs shrink-0 {{ $ph->tipo === 'ganado' ? 'bg-emerald-400/30 text-emerald-200' : 'bg-rose-400/30 text-rose-200' }}">
                                            +{{ $ph->puntos }} pts
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                    <!-- Mis Citas -->
                    <div class="bg-white rounded-3xl p-6 border border-stone-200/50 shadow-sm space-y-6">
                        <div class="flex items-center space-x-3 pb-4 border-b border-stone-100">
                            <div class="w-9 h-9 bg-rose-50 rounded-xl flex items-center justify-center text-rose-500">
                                <i class="far fa-calendar-alt text-base"></i>
                            </div>
                            <h4 class="text-lg font-black text-stone-900">Mis Citas Agendadas</h4>
                        </div>

                        @if($citas->isEmpty())
                            <div class="text-center py-10 space-y-2 text-stone-400">
                                <i class="far fa-calendar-times text-3xl"></i>
                                <p class="text-sm font-bold">No tienes ninguna cita registrada aún.</p>
                                <a href="#servicios" class="text-xs text-amber-600 hover:text-amber-700 font-extrabold underline">Reserva tu primer servicio</a>
                            </div>
                        @else
                            <div class="divide-y divide-stone-100 max-h-96 overflow-y-auto pr-2">
                                @foreach($citas as $cita)
                                    <div class="py-4 flex items-center justify-between hover:bg-stone-50/50 px-2 rounded-xl transition-colors">
                                        <div class="space-y-1 leading-tight">
                                            <p class="text-sm font-extrabold text-stone-800">{{ $cita->servicio->nombre }}</p>
                                            <p class="text-xs text-stone-500">Estilista: {{ $cita->estilista->name ?? 'Por asignar' }}</p>
                                            <div class="text-[10px] text-stone-400 font-bold flex items-center space-x-2">
                                                <span><i class="far fa-calendar mr-0.5"></i> {{ \Carbon\Carbon::parse($cita->fecha)->format('d/m/Y') }}</span>
                                                <span>•</span>
                                                <span><i class="far fa-clock mr-0.5"></i> {{ \Carbon\Carbon::parse($cita->hora)->format('h:i A') }}</span>
                                            </div>
                                        </div>

                                        <div class="text-right space-y-2 shrink-0">
                                            <span class="inline-block px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest 
                                                @if($cita->estado === 'pendiente') bg-amber-50 text-amber-600 border border-amber-200/50
                                                @elseif($cita->estado === 'confirmada') bg-sky-50 text-sky-600 border border-sky-200/50
                                                @elseif($cita->estado === 'completada') bg-emerald-50 text-emerald-600 border border-emerald-200/50
                                                @else bg-rose-50 text-rose-600 border border-rose-200/50 @endif">
                                                {{ $cita->estado }}
                                            </span>
                                            @if($cita->estado === 'completada')
                                                <div class="flex items-center justify-end space-x-2">
                                                    @if($cita->valoracion)
                                                        <span class="text-[10px] text-amber-600 font-bold bg-amber-50 px-2 py-0.5 rounded-lg border border-amber-200/50">
                                                            ★ {{ $cita->valoracion->estrellas }} Reseñada
                                                        </span>
                                                    @else
                                                        <button onclick="openReviewModal('{{ $cita->id }}', '{{ $cita->servicio->nombre ?? 'Servicio' }}')" class="text-[10px] bg-gradient-to-r from-amber-500 to-rose-500 hover:from-amber-600 hover:to-rose-600 text-white font-extrabold px-2.5 py-1 rounded-lg shadow-sm transition-all flex items-center space-x-1">
                                                            <i class="fas fa-star text-[9px]"></i>
                                                            <span>Dar Reseña</span>
                                                        </button>
                                                    @endif
                                                    <a href="{{ route('citas.show-ticket', $cita->id) }}" target="_blank" class="text-[10px] text-indigo-600 hover:text-indigo-700 font-black flex items-center space-x-0.5">
                                                        <i class="fas fa-ticket-alt"></i>
                                                        <span>Ticket</span>
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- Mis Compras -->
                    <div class="bg-white rounded-3xl p-6 border border-stone-200/50 shadow-sm space-y-6">
                        <div class="flex items-center space-x-3 pb-4 border-b border-stone-100">
                            <div class="w-9 h-9 bg-amber-50 rounded-xl flex items-center justify-center text-amber-600">
                                <i class="fas fa-shopping-bag text-base"></i>
                            </div>
                            <h4 class="text-lg font-black text-stone-900">Mis Compras de Productos</h4>
                        </div>

                        @if($compras->isEmpty())
                            <div class="text-center py-10 space-y-2 text-stone-400">
                                <i class="fas fa-receipt text-3xl"></i>
                                <p class="text-sm font-bold">No has realizado ninguna compra de productos todavía.</p>
                                <a href="#productos" class="text-xs text-amber-600 hover:text-amber-700 font-extrabold underline">Visita la tienda online</a>
                            </div>
                        @else
                            <div class="divide-y divide-stone-100 max-h-96 overflow-y-auto pr-2">
                                @foreach($compras as $compra)
                                    <div class="py-4 flex items-center justify-between hover:bg-stone-50/50 px-2 rounded-xl transition-colors">
                                        <div class="space-y-1.5 leading-tight">
                                            <div class="flex items-center space-x-2">
                                                <span class="text-[10px] text-stone-400 font-black uppercase tracking-wider bg-stone-100 px-2 py-0.5 rounded-md">Venta #{{ $compra->id }}</span>
                                                <span class="text-xs text-stone-500 font-bold"><i class="far fa-calendar mr-0.5"></i> {{ \Carbon\Carbon::parse($compra->fecha_venta)->format('d/m/Y H:i') }}</span>
                                            </div>
                                            <div class="text-xs text-stone-700 font-semibold space-y-0.5">
                                                @foreach($compra->detalles as $det)
                                                    <p>{{ $det->producto->nombre }} (x{{ $det->cantidad }})</p>
                                                 @endforeach
                                            </div>
                                        </div>

                                        <div class="text-right space-y-2 shrink-0">
                                            <p class="text-sm font-black text-stone-900">Bs {{ number_format($compra->total, 2) }}</p>
                                            <span class="inline-block px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider 
                                                @if($compra->estado_pago === 'pendiente') bg-amber-50 text-amber-600 border border-amber-200/50
                                                @elseif($compra->estado_pago === 'completado') bg-emerald-50 text-emerald-600 border border-emerald-200/50
                                                @else bg-rose-50 text-rose-600 border border-rose-200/50 @endif">
                                                {{ $compra->estado_pago }}
                                            </span>
                                            @if($compra->estado_pago === 'completado')
                                                <a href="{{ route('ventas.ticket', $compra->id) }}" target="_blank" class="block text-[10px] text-indigo-600 hover:text-indigo-700 font-black flex items-center justify-end space-x-0.5">
                                                    <i class="fas fa-receipt"></i>
                                                    <span>Ticket</span>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>
    @endauth

    <!-- Booking Modal (Solo autenticados) -->
    @auth
        <div id="bookingModal" class="fixed inset-0 z-50 bg-stone-900/60 backdrop-blur-sm hidden items-center justify-center p-4">
            <div class="bg-white rounded-3xl w-full max-w-lg overflow-hidden shadow-2xl border border-stone-100 animate-in fade-in zoom-in-95 duration-200">
                <div class="px-6 py-5 bg-stone-900 text-white flex justify-between items-center">
                    <div class="flex items-center space-x-3">
                        <i class="far fa-calendar-alt text-xl text-amber-400"></i>
                        <div>
                            <h4 class="font-extrabold text-sm uppercase tracking-wider">Agendar Cita de Servicio</h4>
                            <p class="text-[10px] text-stone-400 font-bold" id="bookingServiceName">Servicio Seleccionado</p>
                        </div>
                    </div>
                    <button onclick="closeBookingModal()" class="w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20 flex items-center justify-center transition-colors">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                </div>

                <form action="{{ route('client.appointments.store') }}" method="POST" class="p-6 space-y-4">
                    @csrf
                    <input type="hidden" name="servicio_id" id="bookingServiceId">

                    <!-- Seleccionar Estilista -->
                    <div class="space-y-1.5">
                        <label for="estilista_id" class="text-[11px] font-black text-stone-400 uppercase tracking-widest block">Estilista de Preferencia</label>
                        <select name="estilista_id" id="estilista_id" class="w-full bg-stone-50 border border-stone-200 text-sm font-semibold text-stone-700 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-amber-200 transition-all">
                            <option value="">Cualquier estilista disponible</option>
                            @foreach($estilistas as $estilista)
                                <option value="{{ $estilista->id }}">{{ $estilista->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <!-- Fecha -->
                        <div class="space-y-1.5">
                            <label for="fecha" class="text-[11px] font-black text-stone-400 uppercase tracking-widest block">Fecha</label>
                            <input type="date" name="fecha" id="fecha" required min="{{ date('Y-m-d') }}" class="w-full bg-stone-50 border border-stone-200 text-sm font-semibold text-stone-700 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-amber-200 transition-all">
                        </div>

                        <!-- Hora -->
                        <div class="space-y-1.5">
                            <label for="hora" class="text-[11px] font-black text-stone-400 uppercase tracking-widest block">Hora Disponible</label>
                            <select name="hora" id="hora" required disabled class="w-full bg-stone-50 border border-stone-200 text-sm font-semibold text-stone-700 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-amber-200 transition-all">
                                <option value="">Selecciona fecha primero</option>
                            </select>
                        </div>
                    </div>

                    <!-- Notas -->
                    <div class="space-y-1.5">
                        <label for="notas" class="text-[11px] font-black text-stone-400 uppercase tracking-widest block">Instrucciones o Notas Especiales</label>
                        <textarea name="notas" id="notas" rows="3" placeholder="Ej. Detalles de coloración, alergias, o solicitudes particulares." class="w-full bg-stone-50 border border-stone-200 text-sm font-semibold text-stone-700 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-amber-200 transition-all resize-none"></textarea>
                    </div>

                    <button type="submit" class="w-full bg-stone-900 hover:bg-rose-500 text-white font-extrabold py-3.5 px-4 rounded-xl transition-all text-xs flex items-center justify-center space-x-1.5 shadow-md shadow-stone-200 mt-2">
                        <i class="far fa-calendar-check"></i>
                        <span>Confirmar Reserva</span>
                    </button>
                </form>
            </div>
        </div>
    @endauth

    <!-- Botón Flotante y Modal de Reseñas para Clientes -->
    @auth
        <button onclick="openReviewModal()" class="fixed bottom-6 right-6 z-40 bg-gradient-to-r from-amber-500 to-rose-500 hover:from-amber-600 hover:to-rose-600 text-white font-extrabold px-5 py-3 rounded-full shadow-2xl flex items-center space-x-2 transition-all transform hover:scale-105 active:scale-95 focus:outline-none">
            <i class="fas fa-star text-base animate-bounce"></i>
            <span class="text-xs font-black tracking-wide">Dejar Reseña</span>
            @if(isset($citasSinValorar) && $citasSinValorar->count() > 0)
                <span class="w-5 h-5 bg-white text-rose-600 rounded-full flex items-center justify-center text-[10px] font-black shadow-sm">{{ $citasSinValorar->count() }}</span>
            @endif
        </button>

        <!-- Modal de Valoración / Reseña -->
        <div id="reviewModal" class="fixed inset-0 z-50 bg-stone-900/60 backdrop-blur-sm hidden items-center justify-center p-4">
            <div class="bg-white rounded-3xl w-full max-w-lg overflow-hidden shadow-2xl border border-stone-100 animate-in fade-in zoom-in-95 duration-200">
                <div class="px-6 py-5 bg-gradient-to-r from-amber-500 to-rose-500 text-white flex justify-between items-center">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-star text-xl text-amber-200"></i>
                        <div>
                            <h4 class="font-extrabold text-sm uppercase tracking-wider">Tu Calificación y Opinión</h4>
                            <p class="text-[10px] text-amber-100 font-bold" id="reviewModalSubtitle">Califica el servicio o atención recibida</p>
                        </div>
                    </div>
                    <button onclick="closeReviewModal()" class="w-8 h-8 rounded-lg bg-white/20 hover:bg-white/30 flex items-center justify-center transition-colors text-white">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                </div>

                <form action="{{ route('valoraciones.store') }}" method="POST" class="p-6 space-y-4">
                    @csrf
                    <input type="hidden" name="cita_id" id="reviewCitaId">

                    <!-- Selección de cita si no se pasa de forma directa -->
                    <div id="citaSelectContainer" class="space-y-1.5">
                        <label for="reviewCitaSelect" class="text-[11px] font-black text-stone-400 uppercase tracking-widest block">Servicio a Calificar</label>
                        @if(isset($citasSinValorar) && $citasSinValorar->count() > 0)
                            <select id="reviewCitaSelect" onchange="document.getElementById('reviewCitaId').value = this.value" class="w-full bg-stone-50 border border-stone-200 text-xs font-bold text-stone-700 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-amber-200 transition-all">
                                @foreach($citasSinValorar as $cs)
                                    <option value="{{ $cs->id }}">{{ $cs->servicio->nombre }} - Atendido por {{ $cs->estilista->name ?? 'Estilista' }} ({{ \Carbon\Carbon::parse($cs->fecha)->format('d/m/Y') }})</option>
                                @endforeach
                            </select>
                        @else
                            <div class="p-3 bg-amber-50 border border-amber-200/50 rounded-xl text-xs text-amber-700 font-medium">
                                <i class="fas fa-info-circle mr-1"></i> Reseña u opinión general sobre la atención en Salón Anita.
                            </div>
                        @endif
                    </div>

                    <!-- Puntuación Estrellas -->
                    <div class="space-y-1.5">
                        <label class="text-[11px] font-black text-stone-400 uppercase tracking-widest block">Calificación (1 a 5 Estrellas)</label>
                        <div class="flex items-center justify-between bg-stone-50 p-3 rounded-xl border border-stone-200">
                            <input type="hidden" name="estrellas" id="review_estrellas_input" value="5" required>
                            <div class="flex items-center space-x-1 cursor-pointer text-amber-400 text-2xl" id="review_stars_container">
                                <i class="fas fa-star review-star-btn transition-transform duration-150" data-value="1"></i>
                                <i class="fas fa-star review-star-btn transition-transform duration-150" data-value="2"></i>
                                <i class="fas fa-star review-star-btn transition-transform duration-150" data-value="3"></i>
                                <i class="fas fa-star review-star-btn transition-transform duration-150" data-value="4"></i>
                                <i class="fas fa-star review-star-btn transition-transform duration-150" data-value="5"></i>
                            </div>
                            <span class="text-xs font-black text-amber-600" id="review_star_label">5.0 / Excelente</span>
                        </div>
                    </div>

                    <!-- Comentario -->
                    <div class="space-y-1.5">
                        <label for="reviewComentario" class="text-[11px] font-black text-stone-400 uppercase tracking-widest block">Tu Opinión o Sugerencia (Opcional)</label>
                        <textarea name="comentario" id="reviewComentario" rows="3" placeholder="¿Qué te pareció el trato, la puntualidad o los resultados? Cuéntanos tu experiencia..." class="w-full bg-stone-50 border border-stone-200 text-xs font-medium text-stone-700 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-amber-200 transition-all resize-none"></textarea>
                    </div>

                    <button type="submit" class="w-full bg-gradient-to-r from-amber-500 to-rose-500 hover:from-amber-600 hover:to-rose-600 text-white font-extrabold py-3.5 px-4 rounded-xl transition-all text-xs flex items-center justify-center space-x-1.5 shadow-md shadow-amber-100 mt-2">
                        <i class="fas fa-paper-plane"></i>
                        <span>Publicar Mi Opinión</span>
                    </button>
                </form>
            </div>
        </div>
    @endauth
@endsection

@section('scripts')
    <script>
        // Funciones del Modal de Reservas
        function openBookingModal(serviceId, serviceName) {
            const modal = document.getElementById('bookingModal');
            if (modal) {
                document.getElementById('bookingServiceId').value = serviceId;
                document.getElementById('bookingServiceName').textContent = serviceName;
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                
                // Limpiar y resetear campos de fecha/hora en apertura
                document.getElementById('fecha').value = '';
                const horaSelect = document.getElementById('hora');
                horaSelect.innerHTML = '<option value="">Selecciona fecha primero</option>';
                horaSelect.disabled = true;

                // Resetear estilista y notas
                const estilistaSelect = document.getElementById('estilista_id');
                if (estilistaSelect) {
                    estilistaSelect.value = '';
                }
                const notasTextarea = document.getElementById('notas');
                if (notasTextarea) {
                    notasTextarea.value = '';
                }
            }
        }

        function closeBookingModal() {
            const modal = document.getElementById('bookingModal');
            if (modal) {
                modal.classList.remove('flex');
                modal.classList.add('hidden');
            }
        }

        // Cantidad de productos en la tienda
        function incrementQty(btn, maxStock) {
            const container = btn.closest('div');
            const input = container.querySelector('input[name="cantidad"]');
            let val = parseInt(input.value);
            if (val < maxStock) {
                input.value = val + 1;
            }
        }

        function decrementQty(btn) {
            const container = btn.closest('div');
            const input = container.querySelector('input[name="cantidad"]');
            let val = parseInt(input.value);
            if (val > 1) {
                input.value = val - 1;
            }
        }

        // Lógica de carga dinámica de horas disponibles en el modal de reserva
        const fechaInput = document.getElementById('fecha');
        const estilistaSelect = document.getElementById('estilista_id');
        const horaSelect = document.getElementById('hora');
        const serviceInput = document.getElementById('bookingServiceId');

        function fetchAvailableHours() {
            const fecha = fechaInput.value;
            const estilistaId = estilistaSelect.value;
            const servicioId = serviceInput.value;

            if (!fecha || !servicioId) {
                horaSelect.disabled = true;
                horaSelect.innerHTML = '<option value="">Selecciona fecha primero</option>';
                return;
            }

            horaSelect.disabled = true;
            horaSelect.innerHTML = '<option value="">Cargando horarios...</option>';

            const url = `/client/available-hours?fecha=${fecha}&servicio_id=${servicioId}` + 
                        (estilistaId ? `&estilista_id=${estilistaId}` : '') +
                        `&_=${new Date().getTime()}`;

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    horaSelect.innerHTML = '';
                    if (data.length === 0) {
                        horaSelect.innerHTML = '<option value="">No hay horarios disponibles</option>';
                        horaSelect.disabled = true;
                    } else {
                        data.forEach(slot => {
                            const option = document.createElement('option');
                            option.value = slot.valor;
                            option.textContent = slot.texto;
                            horaSelect.appendChild(option);
                        });
                        horaSelect.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Error al cargar horarios:', error);
                    horaSelect.innerHTML = '<option value="">Error al cargar horarios</option>';
                    horaSelect.disabled = true;
                });
        }

        if (fechaInput) fechaInput.addEventListener('change', fetchAvailableHours);
        if (estilistaSelect) estilistaSelect.addEventListener('change', fetchAvailableHours);

        // Funciones del Modal de Reseñas
        function openReviewModal(citaId = '', servicioNombre = '') {
            const modal = document.getElementById('reviewModal');
            if (!modal) return;

            const citaIdInput = document.getElementById('reviewCitaId');
            const subtitle = document.getElementById('reviewModalSubtitle');
            const selectContainer = document.getElementById('citaSelectContainer');
            const citaSelect = document.getElementById('reviewCitaSelect');

            if (citaId) {
                citaIdInput.value = citaId;
                subtitle.textContent = `Calificando: ${servicioNombre}`;
                if (selectContainer) selectContainer.classList.add('hidden');
            } else {
                if (citaSelect && citaSelect.value) {
                    citaIdInput.value = citaSelect.value;
                } else {
                    citaIdInput.value = '';
                }
                subtitle.textContent = 'Califica el servicio o atención recibida';
                if (selectContainer) selectContainer.classList.remove('hidden');
            }

            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeReviewModal() {
            const modal = document.getElementById('reviewModal');
            if (modal) {
                modal.classList.remove('flex');
                modal.classList.add('hidden');
            }
        }

        // Lógica de estrellas interactivas en el modal de reseñas
        document.addEventListener('DOMContentLoaded', function() {
            const reviewStars = document.querySelectorAll('.review-star-btn');
            const reviewInput = document.getElementById('review_estrellas_input');
            const reviewLabel = document.getElementById('review_star_label');

            if (!reviewStars.length || !reviewInput || !reviewLabel) return;

            const labels = {
                1: '1.0 / Deficiente',
                2: '2.0 / Regular',
                3: '3.0 / Bueno',
                4: '4.0 / Muy Bueno',
                5: '5.0 / Excelente'
            };

            reviewStars.forEach(star => {
                star.addEventListener('click', function() {
                    const val = parseInt(this.getAttribute('data-value'));
                    reviewInput.value = val;
                    reviewLabel.textContent = labels[val] || (val + '.0');

                    reviewStars.forEach((s, idx) => {
                        if (idx < val) {
                            s.classList.remove('text-stone-300');
                            s.classList.add('text-amber-400');
                        } else {
                            s.classList.remove('text-amber-400');
                            s.classList.add('text-stone-300');
                        }
                    });
                });

                star.addEventListener('mouseenter', function() {
                    const val = parseInt(this.getAttribute('data-value'));
                    reviewStars.forEach((s, idx) => {
                        if (idx < val) {
                            s.classList.add('scale-125');
                        }
                    });
                });

                star.addEventListener('mouseleave', function() {
                    reviewStars.forEach(s => s.classList.remove('scale-125'));
                });
            });
        });
    </script>
@endsection
