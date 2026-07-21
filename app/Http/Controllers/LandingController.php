<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Venta;
use App\Models\VentaDetalle;
use App\Models\Producto;
use App\Models\Servicio;
use App\Models\Promocion;
use App\Models\User;
use App\Models\Alerta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\LogsActivity;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class LandingController extends Controller
{
    use LogsActivity;

    public function index()
    {
        $servicios = \Illuminate\Support\Facades\Cache::remember('landing_servicios', 60, function () {
            return Servicio::where('activo', true)->get();
        });

        $productos = \Illuminate\Support\Facades\Cache::remember('landing_productos', 60, function () {
            return Producto::where('stock', '>', 0)->get();
        });
        
        $promociones = \Illuminate\Support\Facades\Cache::remember('landing_promociones', 60, function () {
            return Promocion::where('activo', true)
                ->whereDate('fecha_inicio', '<=', now())
                ->whereDate('fecha_fin', '>=', now())
                ->get();
        });

        $estilistas = \Illuminate\Support\Facades\Cache::remember('landing_estilistas', 60, function () {
            return User::whereHas('role', function($q) {
                $q->where('slug', 'estilista');
            })->get();
        });

        $citas = collect();
        $compras = collect();
        $citasSinValorar = collect();

        if (auth()->check()) {
            $citas = Cita::with(['servicio', 'estilista', 'valoracion'])
                ->where('cliente_id', auth()->id())
                ->orderBy('fecha', 'desc')
                ->orderBy('hora', 'desc')
                ->get();

            $compras = Venta::with(['detalles.producto'])
                ->where('cliente_id', auth()->id())
                ->orderBy('fecha_venta', 'desc')
                ->get();

            $citasSinValorar = Cita::with(['servicio', 'estilista'])
                ->where('cliente_id', auth()->id())
                ->where('estado', 'completada')
                ->whereDoesntHave('valoracion')
                ->get();

            $puntosHistorial = \App\Models\PuntosHistorial::where('user_id', auth()->id())
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get();
        }

        return view('landing', compact('servicios', 'productos', 'promociones', 'estilistas', 'citas', 'compras', 'citasSinValorar', 'puntosHistorial'));
    }

    public function agendarCita(Request $request)
    {
        $request->validate([
            'servicio_id' => 'required|exists:servicios,id',
            'estilista_id' => 'nullable|exists:users,id',
            'fecha' => 'required|date|after_or_equal:today',
            'hora' => 'required',
            'notas' => 'nullable|string'
        ]);

        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para agendar una cita.');
        }

        // Validar disponibilidad del espacio de tiempo y obtener estilista asignado
        $stylistId = $this->isSlotAvailable($request->fecha, $request->servicio_id, $request->estilista_id, $request->hora);

        if (!$stylistId) {
            return back()->withInput()->with('error', 'El horario seleccionado ya no está disponible o es inválido. Por favor, selecciona otro.');
        }

        $cita = Cita::create([
            'cliente_id' => auth()->id(),
            'servicio_id' => $request->servicio_id,
            'estilista_id' => $stylistId,
            'fecha' => $request->fecha,
            'hora' => $request->hora,
            'estado' => 'pendiente',
            'notas' => $request->notas
        ]);

        $this->logActivity('CREATE', "Cita agendada por el cliente vía web. Cita ID: {$cita->id}", $cita->toArray());

        return redirect()->route('landing')->with('success', 'Tu cita ha sido reservada con éxito. Pronto será confirmada por el personal.');
    }

    private function isSlotAvailable($fecha, $servicioId, $estilistaId, $hora)
    {
        $servicio = Servicio::find($servicioId);
        if (!$servicio) {
            return false;
        }
        $duracion = $servicio->duracion_minutos;

        // Convertir día de la semana a español
        $diasSemana = [
            0 => 'Domingo',
            1 => 'Lunes',
            2 => 'Martes',
            3 => 'Miércoles',
            4 => 'Jueves',
            5 => 'Viernes',
            6 => 'Sábado'
        ];
        $dayOfWeekNumber = \Carbon\Carbon::parse($fecha)->dayOfWeek;
        $diaSemana = $diasSemana[$dayOfWeekNumber];

        // Obtener horarios activos de los estilistas para ese día
        $queryHorarios = \App\Models\Horario::where('dia_semana', $diaSemana)
            ->where('activo', true);

        if ($estilistaId) {
            $queryHorarios->where('user_id', $estilistaId);
        } else {
            $queryHorarios->whereHas('user.role', function($q) {
                $q->where('slug', 'estilista');
            });
        }

        $horarios = $queryHorarios->get();
        if ($horarios->isEmpty()) {
            $fallbackUserIds = [];
            if ($estilistaId) {
                $fallbackUserIds[] = (int) $estilistaId;
            } else {
                $fallbackUserIds = User::whereHas('role', function($q) {
                    $q->where('slug', 'estilista');
                })->pluck('id')->toArray();

                if (empty($fallbackUserIds)) {
                    $fallbackUserIds = [1];
                }
            }

            $horarios = collect();
            foreach ($fallbackUserIds as $uid) {
                $horarios->push((object) [
                    'user_id' => $uid,
                    'hora_inicio' => '09:00:00',
                    'hora_fin' => '18:30:00',
                    'activo' => true
                ]);
            }
        }

        try {
            $targetTime = \Carbon\Carbon::createFromFormat('H:i:s', $hora);
        } catch (\Exception $e) {
            try {
                $targetTime = \Carbon\Carbon::createFromFormat('H:i', $hora);
            } catch (\Exception $ex) {
                return false;
            }
        }
        
        $horaFormat = $targetTime->format('H:i:s');

        // Validar que no sea en el pasado si es hoy
        if ($fecha === now()->toDateString()) {
            $limitTime = now()->addMinutes(15);
            $slotDateTime = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $fecha . ' ' . $horaFormat);
            if ($slotDateTime->lessThan($limitTime)) {
                return false;
            }
        }

        // Cargar todas las citas del día para estos estilistas de una sola vez
        $estilistasIds = $horarios->pluck('user_id')->unique()->toArray();
        $todasCitas = Cita::with('servicio')
            ->whereIn('estilista_id', $estilistasIds)
            ->where('fecha', $fecha)
            ->whereIn('estado', ['pendiente', 'confirmada', 'completada'])
            ->get()
            ->groupBy('estilista_id');

        // Buscar estilista disponible para ese bloque de tiempo
        foreach ($horarios as $h) {
            $hInicio = \Carbon\Carbon::createFromFormat('H:i:s', $h->hora_inicio);
            $hFin = \Carbon\Carbon::createFromFormat('H:i:s', $h->hora_fin);

            if ($targetTime->greaterThanOrEqualTo($hInicio) && $targetTime->lessThan($hFin)) {
                $nuevoInicioMin = $targetTime->hour * 60 + $targetTime->minute;
                $nuevoFinMin = $nuevoInicioMin + $duracion;

                $hFinMin = $hFin->hour * 60 + $hFin->minute;
                if ($nuevoFinMin > $hFinMin) {
                    continue;
                }

                $citas = $todasCitas->get($h->user_id, collect());

                $overlap = false;
                foreach ($citas as $c) {
                    $cHora = \Carbon\Carbon::parse($c->hora);
                    $cDuracion = $c->servicio->duracion_minutos ?? 30;

                    $cInicioMin = $cHora->hour * 60 + $cHora->minute;
                    $cFinMin = $cInicioMin + $cDuracion;

                    if ($nuevoInicioMin < $cFinMin && $cInicioMin < $nuevoFinMin) {
                        $overlap = true;
                        break;
                    }
                }

                if (!$overlap) {
                    return $h->user_id;
                }
            }
        }

        return false;
    }

    public function comprarProducto(Request $request)
    {
        $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'cantidad' => 'required|integer|min:1',
        ]);

        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para comprar productos.');
        }

        $producto = Producto::find($request->producto_id);
        $cantidad = (int) $request->cantidad;

        if ($producto->stock < $cantidad) {
            return back()->with('error', "Stock insuficiente para el producto: {$producto->nombre} (Disponibles: {$producto->stock})");
        }

        // Obtener un administrador por defecto para asignarlo como vendedor
        $adminVendedor = User::whereHas('role', function($q) {
            $q->where('slug', 'administrador');
        })->first();

        if (!$adminVendedor) {
            return back()->with('error', 'Error del sistema: No se encontró un vendedor asignado.');
        }

        try {
            DB::beginTransaction();

            $venta = new Venta();
            $venta->cliente_id = auth()->id();
            $venta->cliente_nombre = auth()->user()->name;
            $venta->vendedor_id = $adminVendedor->id;
            $venta->subtotal = 0;
            $venta->descuento = 0;
            $venta->total = 0;
            $venta->metodo_pago = 'stripe';
            $venta->estado_pago = 'pendiente';
            $venta->fecha_venta = now();
            $venta->save();

            // Buscar promoción activa para el producto
            $promocion = Promocion::where('activo', true)
                ->where('producto_id', $producto->id)
                ->whereDate('fecha_inicio', '<=', now())
                ->whereDate('fecha_fin', '>=', now())
                ->first();

            $precioUnitario = $producto->precio_venta;
            $descuentoPorUnidad = 0;

            if ($promocion) {
                $descuentoPorUnidad = ($precioUnitario * $promocion->descuento_porcentaje) / 100;
            }

            $subtotalItem = $cantidad * $precioUnitario;
            $descuentoItem = $cantidad * $descuentoPorUnidad;
            $totalItem = $subtotalItem - $descuentoItem;

            VentaDetalle::create([
                'venta_id' => $venta->id,
                'producto_id' => $producto->id,
                'cantidad' => $cantidad,
                'precio_unitario' => $precioUnitario,
                'descuento' => $descuentoItem,
                'subtotal' => $totalItem
            ]);

            $venta->subtotal = $subtotalItem;
            $venta->descuento = $descuentoItem;
            $venta->total = $totalItem;
            $venta->save();

            DB::commit();

            // Integración de Stripe Checkout
            Stripe::setApiKey(config('services.stripe.secret'));

            $precioConDescuento = $precioUnitario - $descuentoPorUnidad;

            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'bob',
                        'product_data' => [
                            'name' => $producto->nombre,
                            'description' => $producto->descripcion ?? 'Producto de Salón de Belleza',
                        ],
                        'unit_amount' => (int) round($precioConDescuento * 100),
                    ],
                    'quantity' => $cantidad,
                ]],
                'mode' => 'payment',
                'success_url' => route('ventas.stripe.success', $venta->id),
                'cancel_url' => route('ventas.stripe.cancel', $venta->id),
            ]);

            $venta->stripe_session_id = $session->id;
            $venta->save();

            $this->logActivity('CREATE', "Intento de compra de producto en línea. Venta ID: {$venta->id}. Total: Bs{$venta->total}", $venta->load('detalles')->toArray());

            return redirect($session->url);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al procesar el pago: ' . $e->getMessage());
        }
    }

    public function getHorariosDisponibles(Request $request)
    {
        $request->validate([
            'fecha' => 'required|date|after_or_equal:today',
            'servicio_id' => 'required|exists:servicios,id',
            'estilista_id' => 'nullable|exists:users,id',
        ]);

        $fecha = $request->fecha;
        $servicioId = $request->servicio_id;
        $estilistaId = $request->estilista_id;

        $servicio = Servicio::find($servicioId);
        $duracion = $servicio ? $servicio->duracion_minutos : 30;

        // Convertir día de la semana a español
        $diasSemana = [
            0 => 'Domingo',
            1 => 'Lunes',
            2 => 'Martes',
            3 => 'Miércoles',
            4 => 'Jueves',
            5 => 'Viernes',
            6 => 'Sábado'
        ];
        $dayOfWeekNumber = \Carbon\Carbon::parse($fecha)->dayOfWeek;
        $diaSemana = $diasSemana[$dayOfWeekNumber];

        // Obtener horarios activos de los estilistas para ese día
        $queryHorarios = \App\Models\Horario::where('dia_semana', $diaSemana)
            ->where('activo', true);

        if ($estilistaId) {
            $queryHorarios->where('user_id', $estilistaId);
        } else {
            $queryHorarios->whereHas('user.role', function($q) {
                $q->where('slug', 'estilista');
            });
        }

        $horarios = $queryHorarios->get();

        if ($horarios->isEmpty()) {
            $fallbackUserIds = [];
            if ($estilistaId) {
                $fallbackUserIds[] = (int) $estilistaId;
            } else {
                $fallbackUserIds = User::whereHas('role', function($q) {
                    $q->where('slug', 'estilista');
                })->pluck('id')->toArray();

                if (empty($fallbackUserIds)) {
                    $fallbackUserIds = [1];
                }
            }

            $horarios = collect();
            foreach ($fallbackUserIds as $uid) {
                $horarios->push((object) [
                    'user_id' => $uid,
                    'hora_inicio' => '09:00:00',
                    'hora_fin' => '18:30:00',
                    'activo' => true
                ]);
            }
        }

        // Cargar todas las citas del día para estos estilistas de una sola vez
        $estilistasIds = $horarios->pluck('user_id')->unique()->toArray();
        $todasCitas = Cita::with('servicio')
            ->whereIn('estilista_id', $estilistasIds)
            ->where('fecha', $fecha)
            ->whereIn('estado', ['pendiente', 'confirmada', 'completada'])
            ->get()
            ->groupBy('estilista_id');

        $slots = [];
        $startTime = \Carbon\Carbon::createFromFormat('H:i:s', '08:00:00');
        $endTime = \Carbon\Carbon::createFromFormat('H:i:s', '20:00:00');

        $isToday = $fecha === now()->toDateString();
        $limitTime = now()->addMinutes(15);

        while ($startTime->lessThanOrEqualTo($endTime)) {
            $slotTimeStr = $startTime->format('H:i:s');
            
            if ($isToday) {
                $slotDateTime = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $fecha . ' ' . $slotTimeStr);
                if ($slotDateTime->lessThan($limitTime)) {
                    $startTime->addMinutes(30);
                    continue;
                }
            }

            $disponible = false;

            foreach ($horarios as $h) {
                $hInicio = \Carbon\Carbon::createFromFormat('H:i:s', $h->hora_inicio);
                $hFin = \Carbon\Carbon::createFromFormat('H:i:s', $h->hora_fin);

                if ($startTime->greaterThanOrEqualTo($hInicio) && $startTime->lessThan($hFin)) {
                    $nuevoInicioMin = $startTime->hour * 60 + $startTime->minute;
                    $nuevoFinMin = $nuevoInicioMin + $duracion;

                    $hFinMin = $hFin->hour * 60 + $hFin->minute;
                    if ($nuevoFinMin > $hFinMin) {
                        continue;
                    }

                    $citas = $todasCitas->get($h->user_id, collect());

                    $overlap = false;
                    foreach ($citas as $c) {
                        $cHora = \Carbon\Carbon::parse($c->hora);
                        $cDuracion = $c->servicio->duracion_minutos ?? 30;

                        $cInicioMin = $cHora->hour * 60 + $cHora->minute;
                        $cFinMin = $cInicioMin + $cDuracion;

                        if ($nuevoInicioMin < $cFinMin && $cInicioMin < $nuevoFinMin) {
                            $overlap = true;
                            break;
                        }
                    }

                    if (!$overlap) {
                        $disponible = true;
                        break;
                    }
                }
            }

            if ($disponible) {
                $slots[] = [
                    'valor' => $startTime->format('H:i:s'),
                    'texto' => $startTime->format('h:i A')
                ];
            }

            $startTime->addMinutes(30);
        }

        return response()->json($slots)->header('Cache-Control', 'no-cache, no-store, must-revalidate');
    }
}
