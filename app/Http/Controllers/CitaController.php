<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\User;
use App\Models\Servicio;
use App\Models\Promocion;
use App\Models\Comision;
use App\Models\PuntosHistorial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\LogsActivity;

class CitaController extends Controller
{
    use LogsActivity;

    public function index(Request $request)
    {
        $user = auth()->user();
        
        $query = Cita::with(['cliente', 'estilista', 'servicio']);

        if ($user->hasRole('estilista')) {
            $query->where('estilista_id', $user->id);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $searchLower = mb_strtolower($search, 'UTF-8');
            $query->where(function($q) use ($searchLower) {
                $q->whereRaw('LOWER(COALESCE(estado, \'\')) LIKE ?', ["%{$searchLower}%"])
                  ->orWhereRaw('LOWER(COALESCE(notas, \'\')) LIKE ?', ["%{$searchLower}%"])
                  ->orWhereRaw('CAST(fecha AS text) LIKE ?', ["%{$searchLower}%"])
                  ->orWhereRaw('CAST(hora AS text) LIKE ?', ["%{$searchLower}%"])
                  ->orWhereHas('cliente', function($qc) use ($searchLower) {
                      $qc->whereRaw('LOWER(COALESCE(name, \'\')) LIKE ?', ["%{$searchLower}%"])
                         ->orWhereRaw('LOWER(COALESCE(email, \'\')) LIKE ?', ["%{$searchLower}%"])
                         ->orWhereRaw('LOWER(COALESCE(telefono, \'\')) LIKE ?', ["%{$searchLower}%"]);
                  })->orWhereHas('estilista', function($qe) use ($searchLower) {
                      $qe->whereRaw('LOWER(COALESCE(name, \'\')) LIKE ?', ["%{$searchLower}%"]);
                  })->orWhereHas('servicio', function($qs) use ($searchLower) {
                      $qs->whereRaw('LOWER(COALESCE(nombre, \'\')) LIKE ?', ["%{$searchLower}%"])
                         ->orWhereRaw('CAST(precio AS text) LIKE ?', ["%{$searchLower}%"]);
                  });
            });
        }

        $citas = $query->orderBy('fecha', 'desc')
            ->orderBy('hora', 'desc')
            ->paginate(15)
            ->withQueryString();

        // Fetch all stylists for the quick assignment dropdown/modal
        $estilistas = User::whereHas('role', function($q) { 
            $q->where('slug', 'estilista'); 
        })->get();

        return view('citas.index', compact('citas', 'estilistas'));
    }

    // CU11 - Agendar Cita
    public function create(Request $request)
    {
        $clientes = User::whereHas('role', function($q) { $q->where('slug', 'cliente'); })->get();
        $estilistas = User::whereHas('role', function($q) { $q->where('slug', 'estilista'); })->get();
        $servicios = Servicio::where('activo', true)->get();
        
        // Si se pasa un cliente_id preseleccionado desde la vista de clientes
        $selectedClienteId = $request->query('cliente_id');

        // Fetch promotions for services to display them
        $promociones = Promocion::where('activo', true)
            ->whereNotNull('servicio_id')
            ->whereDate('fecha_inicio', '<=', now())
            ->whereDate('fecha_fin', '>=', now())
            ->get()
            ->keyBy('servicio_id');
        
        return view('citas.create', compact('clientes', 'estilistas', 'servicios', 'selectedClienteId', 'promociones'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required|exists:users,id',
            'servicio_id' => 'required|exists:servicios,id',
            'fecha' => 'required|date',
            'hora' => 'required',
            'estilista_id' => 'nullable|exists:users,id',
            'notas' => 'nullable|string'
        ]);

        $cita = Cita::create($request->all());

        $this->logActivity('CREATE', "Cita agendada para el cliente ID: {$cita->cliente_id}", $cita->toArray());

        return redirect()->route('citas.index')->with('success', 'Cita agendada exitosamente.');
    }

    public function edit(Cita $cita)
    {
        $clientes = User::whereHas('role', function($q) { $q->where('slug', 'cliente'); })->get();
        $estilistas = User::whereHas('role', function($q) { $q->where('slug', 'estilista'); })->get();
        $servicios = Servicio::where('activo', true)->get();
        
        return view('citas.edit', compact('cita', 'clientes', 'estilistas', 'servicios'));
    }

    public function update(Request $request, Cita $cita)
    {
        $request->validate([
            'cliente_id' => 'required|exists:users,id',
            'servicio_id' => 'required|exists:servicios,id',
            'fecha' => 'required|date',
            'hora' => 'required',
            'estilista_id' => 'nullable|exists:users,id',
            'estado' => 'required|in:pendiente,confirmada,completada,cancelada',
            'notas' => 'nullable|string'
        ]);

        $oldData = $cita->toArray();
        $cita->update($request->all());

        $this->logActivity('UPDATE', "Cita actualizada ID: {$cita->id}", [
            'old' => $oldData,
            'new' => $cita->fresh()->toArray()
        ]);

        return redirect()->route('citas.index')->with('success', 'Cita actualizada exitosamente.');
    }

    public function destroy(Cita $cita)
    {
        $id = $cita->id;
        $cita->delete();

        $this->logActivity('DELETE', "Cita eliminada ID: {$id}", []);

        return redirect()->route('citas.index')->with('success', 'Cita eliminada exitosamente.');
    }

    // CU12 - Asignar Estilista a Servicio
    public function asignarEstilista(Request $request, Cita $cita)
    {
        $request->validate([
            'estilista_id' => 'required|exists:users,id',
        ]);

        $estilista = User::find($request->estilista_id);
        if (!$estilista->hasRole('estilista')) {
            return back()->with('error', 'El usuario seleccionado no es un estilista.');
        }

        $oldData = $cita->toArray();
        $cita->update([
            'estilista_id' => $request->estilista_id,
            'estado' => $cita->estado === 'pendiente' ? 'confirmada' : $cita->estado // auto confirm if pending
        ]);

        $this->logActivity('UPDATE', "Estilista asignado a cita ID: {$cita->id}", [
            'old' => $oldData,
            'new' => $cita->fresh()->toArray()
        ]);

        return redirect()->route('citas.index')->with('success', "Estilista {$estilista->name} asignado exitosamente.");
    }

    // CU18 - Registrar Servicio Realizado & CU8 - Calcular Comisión
    public function completar(Request $request, Cita $cita)
    {
        if ($cita->estado === 'completada') {
            return back()->with('error', 'Esta cita ya ha sido registrada como completada.');
        }

        if (!$cita->estilista_id) {
            return back()->with('error', 'Debe asignar un estilista a la cita antes de completarla.');
        }

        try {
            DB::beginTransaction();

            // 1. Update appointment state to completed
            $cita->estado = 'completada';
            $cita->save();

            // 2. Fetch service price & check for promotions (CU17)
            $servicio = $cita->servicio;
            $precioOriginal = $servicio->precio;
            
            $promocion = Promocion::where('activo', true)
                ->where('servicio_id', $servicio->id)
                ->whereDate('fecha_inicio', '<=', $cita->fecha)
                ->whereDate('fecha_fin', '>=', $cita->fecha)
                ->first();

            $descuento = 0;
            if ($promocion) {
                $descuento = ($precioOriginal * $promocion->descuento_porcentaje) / 100;
            }
            $precioFinal = max(0, $precioOriginal - $descuento);

            // Canje de puntos de fidelidad si fue solicitado
            $descuentoPuntos = 0;
            if ($request->filled('puntos_canjear') && $cita->cliente_id) {
                $cliente = User::find($cita->cliente_id);
                $puntosPedidos = (int) $request->puntos_canjear;
                if ($cliente && $puntosPedidos > 0 && $cliente->puntos > 0) {
                    $puntosEfectivos = min($puntosPedidos, $cliente->puntos, (int) floor($precioFinal));
                    if ($puntosEfectivos > 0) {
                        $descuentoPuntos = $puntosEfectivos;
                        $precioFinal = max(0, $precioFinal - $descuentoPuntos);
                        $cliente->decrement('puntos', $puntosEfectivos);

                        \App\Models\PuntosHistorial::create([
                            'user_id' => $cliente->id,
                            'puntos' => $puntosEfectivos,
                            'tipo' => 'canjeado',
                            'descripcion' => "Descuento por canje de {$puntosEfectivos} puntos en Cita ID: {$cita->id}",
                            'cita_id' => $cita->id
                        ]);
                    }
                }
            }

            // 3. Calculate Stylist Commission (CU8)
            $estilista = User::find($cita->estilista_id);
            $porcentajeComision = $estilista->comision_porcentaje ?: 15.00;
            $montoComision = ($precioFinal * $porcentajeComision) / 100;

            // 4. Save commission record
            Comision::create([
                'estilista_id' => $cita->estilista_id,
                'cita_id' => $cita->id,
                'monto_servicio' => $precioFinal,
                'porcentaje_comision' => $porcentajeComision,
                'monto_comision' => $montoComision,
                'estado' => 'pendiente',
                'fecha_calculo' => now()
            ]);

            // 5. Acumular Puntos de Lealtad (Opción 2: 1 punto por cada 10 Bs)
            $puntosGanados = (int) floor($precioFinal / 10);
            if ($puntosGanados > 0 && $cita->cliente_id) {
                $cliente = User::find($cita->cliente_id);
                if ($cliente) {
                    $cliente->increment('puntos', $puntosGanados);

                    PuntosHistorial::create([
                        'user_id' => $cliente->id,
                        'puntos' => $puntosGanados,
                        'tipo' => 'ganado',
                        'descripcion' => "Ganado por servicio completado (Cita ID: {$cita->id})",
                        'cita_id' => $cita->id
                    ]);
                }
            }

            DB::commit();

            $this->logActivity('UPDATE', "Servicio realizado registrado para Cita ID: {$cita->id}. Comisión de Bs{$montoComision} calculada para {$estilista->name}.", [
                'cita_id' => $cita->id,
                'monto_pagado' => $precioFinal,
                'comision' => $montoComision
            ]);

            return redirect()->route('citas.show-ticket', $cita->id)
                ->with('success', 'Servicio registrado como completado. Comisión calculada correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al completar el servicio: ' . $e->getMessage());
        }
    }

    // CU19 - Generar Ticket Servicio
    public function showTicket(Cita $cita)
    {
        $user = auth()->user();
        $isOwner = $cita->cliente_id === $user->id;
        $isStylist = $cita->estilista_id === $user->id;
        $hasPermission = $user->hasPermission('manage_appointments');

        if (!$isOwner && !$isStylist && !$hasPermission) {
            abort(403, 'No tienes permiso para ver este ticket.');
        }

        if ($cita->estado !== 'completada') {
            if ($hasPermission) {
                return redirect()->route('citas.index')->with('error', 'El ticket solo se puede generar para servicios completados.');
            } else {
                return redirect()->route('landing')->with('error', 'El ticket solo se puede generar para servicios completados.');
            }
        }

        $cita->load(['cliente', 'estilista', 'servicio']);
        
        // Retrieve calculated commission/discount details
        $comision = Comision::where('cita_id', $cita->id)->first();
        
        $promocion = Promocion::where('activo', true)
            ->where('servicio_id', $cita->servicio_id)
            ->whereDate('fecha_inicio', '<=', $cita->fecha)
            ->whereDate('fecha_fin', '>=', $cita->fecha)
            ->first();

        return view('citas.ticket', compact('cita', 'comision', 'promocion'));
    }
}
