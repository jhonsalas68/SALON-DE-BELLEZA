<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\VentaDetalle;
use App\Models\Producto;
use App\Models\User;
use App\Models\Promocion;
use App\Models\Alerta;
use App\Models\PuntosHistorial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\LogsActivity;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class VentaController extends Controller
{
    use LogsActivity;

    public function index(Request $request)
    {
        $query = Venta::with(['cliente', 'vendedor']);

        if ($request->filled('search')) {
            $search = trim($request->search);
            $searchLower = mb_strtolower($search, 'UTF-8');
            $query->where(function($q) use ($searchLower) {
                $q->whereRaw('LOWER(COALESCE(cliente_nombre, \'\')) LIKE ?', ["%{$searchLower}%"])
                  ->orWhereRaw('LOWER(COALESCE(metodo_pago, \'\')) LIKE ?', ["%{$searchLower}%"])
                  ->orWhereRaw('LOWER(COALESCE(estado_pago, \'\')) LIKE ?', ["%{$searchLower}%"])
                  ->orWhereRaw('CAST(total AS text) LIKE ?', ["%{$searchLower}%"])
                  ->orWhereRaw('CAST(fecha_venta AS text) LIKE ?', ["%{$searchLower}%"])
                  ->orWhereHas('cliente', function($qc) use ($searchLower) {
                      $qc->whereRaw('LOWER(COALESCE(name, \'\')) LIKE ?', ["%{$searchLower}%"])
                         ->orWhereRaw('LOWER(COALESCE(email, \'\')) LIKE ?', ["%{$searchLower}%"]);
                  })->orWhereHas('vendedor', function($qv) use ($searchLower) {
                      $qv->whereRaw('LOWER(COALESCE(name, \'\')) LIKE ?', ["%{$searchLower}%"]);
                  })->orWhereHas('detalles.producto', function($qp) use ($searchLower) {
                      $qp->whereRaw('LOWER(COALESCE(nombre, \'\')) LIKE ?', ["%{$searchLower}%"])
                         ->orWhereRaw('LOWER(COALESCE(codigo, \'\')) LIKE ?', ["%{$searchLower}%"]);
                  });
            });
        }

        $ventas = $query->orderBy('fecha_venta', 'desc')
            ->paginate(15)
            ->withQueryString();
            
        return view('ventas.index', compact('ventas'));
    }

    public function create()
    {
        $clientes = User::whereHas('role', function($q) { $q->where('slug', 'cliente'); })->get();
        // Fetch active products with stock > 0
        $productos = Producto::where('stock', '>', 0)->get();
        
        // Fetch active promotions for products
        $promociones = Promocion::where('activo', true)
            ->whereNotNull('producto_id')
            ->whereDate('fecha_inicio', '<=', now())
            ->whereDate('fecha_fin', '>=', now())
            ->get()
            ->keyBy('producto_id');

        return view('ventas.create', compact('clientes', 'productos', 'promociones'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cliente_id' => 'nullable|exists:users,id',
            'cliente_nombre' => 'nullable|string|max:255',
            'metodo_pago' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.producto_id' => 'required|exists:productos,id',
            'items.*.cantidad' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            $venta = new Venta();
            $venta->cliente_id = $request->cliente_id;
            $venta->cliente_nombre = $request->cliente_nombre ?: ($request->cliente_id ? User::find($request->cliente_id)->name : 'Cliente Casual');
            $venta->vendedor_id = auth()->user()->id;
            $venta->subtotal = 0;
            $venta->descuento = 0;
            $venta->total = 0;
            $venta->metodo_pago = $request->metodo_pago;
            $venta->fecha_venta = now();
            
            if ($request->metodo_pago === 'stripe') {
                $venta->estado_pago = 'pendiente';
            } else {
                $venta->estado_pago = 'completado';
            }

            $venta->save();

            $subtotalAcumulado = 0;
            $descuentoAcumulado = 0;

            foreach ($request->items as $item) {
                $producto = Producto::find($item['producto_id']);
                $cantidad = (int) $item['cantidad'];

                if ($producto->stock < $cantidad) {
                    throw new \Exception("Stock insuficiente para el producto: {$producto->nombre} (Disponible: {$producto->stock})");
                }

                // Check for active promotion
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

                if ($request->metodo_pago !== 'stripe') {
                    // Decrement stock immediately for traditional payments
                    $producto->stock -= $cantidad;
                    $producto->save();

                    // CU15: Generar Alerta Stock Bajo
                    if ($producto->stock <= $producto->stock_minimo) {
                        $alertaExiste = Alerta::where('producto_id', $producto->id)
                            ->where('leido', false)
                            ->exists();

                        if (!$alertaExiste) {
                            Alerta::create([
                                'tipo' => 'stock_bajo',
                                'mensaje' => "El producto '{$producto->nombre}' ha alcanzado el stock mínimo (Disponible: {$producto->stock}, Mínimo: {$producto->stock_minimo}).",
                                'producto_id' => $producto->id,
                            ]);
                        }
                    }
                }

                VentaDetalle::create([
                    'venta_id' => $venta->id,
                    'producto_id' => $producto->id,
                    'cantidad' => $cantidad,
                    'precio_unitario' => $precioUnitario,
                    'descuento' => $descuentoItem,
                    'subtotal' => $totalItem
                ]);

                $subtotalAcumulado += $subtotalItem;
                $descuentoAcumulado += $descuentoItem;
            }

            // Procesar canje de puntos de fidelidad para descuento si fue solicitado
            $descuentoPuntos = 0;
            if ($request->filled('usar_puntos') && $request->filled('puntos_canjear') && $venta->cliente_id) {
                $cliente = User::find($venta->cliente_id);
                $puntosPedidos = (int) $request->puntos_canjear;
                if ($cliente && $puntosPedidos > 0 && $cliente->puntos > 0) {
                    $totalAntesPuntos = max(0, $subtotalAcumulado - $descuentoAcumulado);
                    $puntosEfectivos = min($puntosPedidos, $cliente->puntos, (int) floor($totalAntesPuntos));

                    if ($puntosEfectivos > 0) {
                        $descuentoPuntos = $puntosEfectivos;
                        $cliente->decrement('puntos', $puntosEfectivos);

                        \App\Models\PuntosHistorial::create([
                            'user_id' => $cliente->id,
                            'puntos' => $puntosEfectivos,
                            'tipo' => 'canjeado',
                            'descripcion' => "Descuento por canje de {$puntosEfectivos} puntos en Venta ID: {$venta->id}",
                            'venta_id' => $venta->id
                        ]);
                    }
                }
            }

            // Update main sale values
            $venta->subtotal = $subtotalAcumulado;
            $venta->descuento = $descuentoAcumulado + $descuentoPuntos;
            $venta->total = max(0, $subtotalAcumulado - $descuentoAcumulado - $descuentoPuntos);
            $venta->save();

            // Ocurrencia de puntos de lealtad (Opción 2)
            if ($venta->cliente_id && $venta->estado_pago === 'completado') {
                $puntosGanados = (int) floor($venta->total / 10);
                if ($puntosGanados > 0) {
                    $cliente = User::find($venta->cliente_id);
                    if ($cliente) {
                        $cliente->increment('puntos', $puntosGanados);
                        PuntosHistorial::create([
                            'user_id' => $cliente->id,
                            'puntos' => $puntosGanados,
                            'tipo' => 'ganado',
                            'descripcion' => "Ganado por compra de productos (Venta ID: {$venta->id})",
                            'venta_id' => $venta->id
                        ]);
                    }
                }
            }

            DB::commit();

            if ($request->metodo_pago === 'stripe') {
                Stripe::setApiKey(config('services.stripe.secret'));

                $lineItems = [];
                $venta->load('detalles.producto');
                foreach ($venta->detalles as $detalle) {
                    $precioConDescuento = $detalle->precio_unitario - ($detalle->descuento / $detalle->cantidad);
                    $lineItems[] = [
                        'price_data' => [
                            'currency' => 'bob',
                            'product_data' => [
                                'name' => $detalle->producto->nombre,
                            ],
                            'unit_amount' => (int) round($precioConDescuento * 100),
                        ],
                        'quantity' => $detalle->cantidad,
                    ];
                }

                $session = Session::create([
                    'payment_method_types' => ['card'],
                    'line_items' => $lineItems,
                    'mode' => 'payment',
                    'success_url' => route('ventas.stripe.success', $venta->id),
                    'cancel_url' => route('ventas.stripe.cancel', $venta->id),
                ]);

                $venta->stripe_session_id = $session->id;
                $venta->save();

                return redirect($session->url);
            }

            $this->logActivity('CREATE', "Venta registrada ID: {$venta->id}. Total: Bs{$venta->total}", $venta->load('detalles')->toArray());

            return redirect()->route('ventas.show', $venta->id)
                ->with('success', 'Venta registrada exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error al registrar venta: ' . $e->getMessage());
        }
    }

    public function stripeSuccess(Venta $venta)
    {
        $hasSalesPermission = auth()->check() && auth()->user()->hasPermission('manage_sales');
        $redirectShow = $hasSalesPermission ? route('ventas.show', $venta->id) : route('landing');
        $redirectIndex = $hasSalesPermission ? route('ventas.index') : route('landing');

        if ($venta->estado_pago === 'completado') {
            return redirect($redirectShow)->with('success', 'La venta ya fue pagada y completada.');
        }

        if (empty($venta->stripe_session_id)) {
            return redirect($redirectIndex)->with('error', 'Sesión de Stripe no encontrada.');
        }

        try {
            Stripe::setApiKey(config('services.stripe.secret'));
            $session = Session::retrieve($venta->stripe_session_id);

            if ($session->payment_status === 'paid') {
                DB::beginTransaction();

                // Double check status after transaction start
                $venta->refresh();
                if ($venta->estado_pago === 'completado') {
                    DB::rollBack();
                    return redirect($redirectShow)->with('success', 'La venta ya fue pagada y completada.');
                }

                $venta->load('detalles.producto');
                foreach ($venta->detalles as $detalle) {
                    $producto = $detalle->producto;
                    $producto->stock -= $detalle->cantidad;
                    $producto->save();

                    // CU15: Generar Alerta Stock Bajo
                    if ($producto->stock <= $producto->stock_minimo) {
                        $alertaExiste = Alerta::where('producto_id', $producto->id)
                            ->where('leido', false)
                            ->exists();

                        if (!$alertaExiste) {
                            Alerta::create([
                                'tipo' => 'stock_bajo',
                                'mensaje' => "El producto '{$producto->nombre}' ha alcanzado el stock mínimo (Disponible: {$producto->stock}, Mínimo: {$producto->stock_minimo}).",
                                'producto_id' => $producto->id,
                            ]);
                        }
                    }
                }

                $venta->estado_pago = 'completado';
                $venta->save();

                // Acumular Puntos de Fidelidad (1 punto cada 10 Bs)
                if ($venta->cliente_id) {
                    $puntosGanados = (int) floor($venta->total / 10);
                    if ($puntosGanados > 0) {
                        $cliente = User::find($venta->cliente_id);
                        if ($cliente) {
                            $cliente->increment('puntos', $puntosGanados);
                            \App\Models\PuntosHistorial::create([
                                'user_id' => $cliente->id,
                                'puntos' => $puntosGanados,
                                'tipo' => 'ganado',
                                'descripcion' => "Ganado por compra de productos en línea (Venta ID: {$venta->id})",
                                'venta_id' => $venta->id
                            ]);
                        }
                    }
                }

                DB::commit();

                $this->logActivity('PAYMENT_COMPLETE', "Pago de venta completado vía Stripe ID: {$venta->id}. Total: Bs{$venta->total}", $venta->load('detalles')->toArray());

                return redirect($redirectShow)->with('success', 'Venta pagada y registrada exitosamente.');
            } else {
                return redirect($redirectShow)->with('error', 'El pago no ha sido completado en Stripe.');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect($redirectIndex)->with('error', 'Error al procesar el pago de Stripe: ' . $e->getMessage());
        }
    }

    public function stripeCancel(Venta $venta)
    {
        $hasSalesPermission = auth()->check() && auth()->user()->hasPermission('manage_sales');
        $redirectShow = $hasSalesPermission ? route('ventas.show', $venta->id) : route('landing');
        $redirectIndex = $hasSalesPermission ? route('ventas.index') : route('landing');

        if ($venta->estado_pago === 'pendiente') {
            $venta->estado_pago = 'cancelado';
            $venta->save();

            $this->logActivity('PAYMENT_CANCELLED', "Pago de venta cancelado vía Stripe ID: {$venta->id}", $venta->toArray());

            return redirect($redirectIndex)->with('warning', 'El pago de la venta fue cancelado por el usuario.');
        }

        return redirect($redirectShow);
    }

    public function show(Venta $venta)
    {
        $venta->load(['cliente', 'vendedor', 'detalles.producto']);
        return view('ventas.show', compact('venta'));
    }

    // CU23: Generar Ticket Producto
    public function ticket(Venta $venta)
    {
        // Security check: only allow owner or user with permission
        if (!auth()->user()->hasPermission('manage_sales') && $venta->cliente_id !== auth()->id()) {
            abort(403, 'No tienes permiso para ver este ticket.');
        }

        $venta->load(['cliente', 'vendedor', 'detalles.producto']);
        return view('ventas.ticket', compact('venta'));
    }

    public function updateStatus(Request $request, Venta $venta)
    {
        $request->validate([
            'estado_pago' => 'required|in:completado,cancelado',
        ]);

        if ($venta->estado_pago !== 'pendiente') {
            return back()->with('error', 'El estado de pago de esta venta ya no se puede modificar.');
        }

        try {
            DB::beginTransaction();

            $venta->estado_pago = $request->estado_pago;

            if ($request->estado_pago === 'completado') {
                $venta->load('detalles.producto');
                foreach ($venta->detalles as $detalle) {
                    $producto = $detalle->producto;
                    if ($producto->stock < $detalle->cantidad) {
                        throw new \Exception("Stock insuficiente para el producto: {$producto->nombre} (Disponible: {$producto->stock})");
                    }
                    $producto->stock -= $detalle->cantidad;
                    $producto->save();

                    // CU15: Generar Alerta Stock Bajo
                    if ($producto->stock <= $producto->stock_minimo) {
                        $alertaExiste = Alerta::where('producto_id', $producto->id)
                            ->where('leido', false)
                            ->exists();

                        if (!$alertaExiste) {
                            Alerta::create([
                                'tipo' => 'stock_bajo',
                                'mensaje' => "El producto '{$producto->nombre}' ha alcanzado el stock mínimo (Disponible: {$producto->stock}, Mínimo: {$producto->stock_minimo}).",
                                'producto_id' => $producto->id,
                            ]);
                        }
                    }
                }
                // Acumular Puntos de Fidelidad (1 punto cada 10 Bs)
                if ($venta->cliente_id) {
                    $puntosGanados = (int) floor($venta->total / 10);
                    if ($puntosGanados > 0) {
                        $cliente = User::find($venta->cliente_id);
                        if ($cliente) {
                            $cliente->increment('puntos', $puntosGanados);
                            \App\Models\PuntosHistorial::create([
                                'user_id' => $cliente->id,
                                'puntos' => $puntosGanados,
                                'tipo' => 'ganado',
                                'descripcion' => "Ganado por compra de productos (Venta ID: {$venta->id})",
                                'venta_id' => $venta->id
                            ]);
                        }
                    }
                }
            }

            $venta->save();
            DB::commit();

            $this->logActivity('UPDATE', "Estado de pago de venta ID: {$venta->id} actualizado a: {$request->estado_pago}", $venta->load('detalles')->toArray());

            return back()->with('success', "Estado de pago actualizado a {$request->estado_pago} exitosamente.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al actualizar el estado de pago: ' . $e->getMessage());
        }
    }
}
