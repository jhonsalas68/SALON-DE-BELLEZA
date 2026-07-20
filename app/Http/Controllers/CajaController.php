<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\MovimientoCaja;
use App\Models\Venta;
use App\Models\Cita;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\LogsActivity;

class CajaController extends Controller
{
    use LogsActivity;

    public function index()
    {
        $cajaActual = Caja::where('estado', 'abierta')->latest()->first();
        
        $montoVentasEfectivo = 0;
        $montoServiciosEfectivo = 0;
        $montoIngresos = 0;
        $montoEgresos = 0;
        $montoEsperado = 0;

        if ($cajaActual) {
            // Calcular ventas de productos pagadas en efectivo durante este turno de caja
            $montoVentasEfectivo = Venta::where('fecha_venta', '>=', $cajaActual->fecha_apertura)
                ->where('metodo_pago', 'efectivo')
                ->where('estado_pago', 'completado')
                ->sum('total');

            // Calcular servicios completados en este turno
            $montoServiciosEfectivo = Cita::where('updated_at', '>=', $cajaActual->fecha_apertura)
                ->where('estado', 'completada')
                ->join('servicios', 'citas.servicio_id', '=', 'servicios.id')
                ->sum('servicios.precio');

            // Movimientos de caja chicas
            $montoIngresos = $cajaActual->movimientos()->where('tipo', 'ingreso')->sum('monto');
            $montoEgresos = $cajaActual->movimientos()->where('tipo', 'egreso')->sum('monto');

            $montoEsperado = $cajaActual->monto_apertura + $montoVentasEfectivo + $montoServiciosEfectivo + $montoIngresos - $montoEgresos;
        }

        $historialCajas = Caja::with('user')
            ->orderBy('fecha_apertura', 'desc')
            ->paginate(10);

        return view('cajas.index', compact(
            'cajaActual',
            'montoVentasEfectivo',
            'montoServiciosEfectivo',
            'montoIngresos',
            'montoEgresos',
            'montoEsperado',
            'historialCajas'
        ));
    }

    public function abrir(Request $request)
    {
        $request->validate([
            'monto_apertura' => 'required|numeric|min:0',
            'observaciones' => 'nullable|string'
        ]);

        $cajaAbierta = Caja::where('estado', 'abierta')->exists();
        if ($cajaAbierta) {
            return back()->with('error', 'Ya existe un turno de caja abierto actualmente.');
        }

        $caja = Caja::create([
            'user_id' => auth()->id(),
            'monto_apertura' => $request->monto_apertura,
            'estado' => 'abierta',
            'fecha_apertura' => now(),
            'observaciones' => $request->observaciones
        ]);

        $this->logActivity('OPEN_CASH', "Turno de caja abierto con monto inicial de Bs{$caja->monto_apertura}", $caja->toArray());

        return redirect()->route('cajas.index')->with('success', 'Turno de caja abierto exitosamente.');
    }

    public function cerrar(Request $request, Caja $caja)
    {
        $request->validate([
            'monto_cierre_efectivo' => 'required|numeric|min:0',
            'observaciones' => 'nullable|string'
        ]);

        if ($caja->estado !== 'abierta') {
            return back()->with('error', 'Este turno de caja ya se encuentra cerrado.');
        }

        // Re-calcular total esperado en efectivo
        $montoVentasEfectivo = Venta::where('fecha_venta', '>=', $caja->fecha_apertura)
            ->where('metodo_pago', 'efectivo')
            ->where('estado_pago', 'completado')
            ->sum('total');

        $montoServiciosEfectivo = Cita::where('updated_at', '>=', $caja->fecha_apertura)
            ->where('estado', 'completada')
            ->join('servicios', 'citas.servicio_id', '=', 'servicios.id')
            ->sum('servicios.precio');

        $montoIngresos = $caja->movimientos()->where('tipo', 'ingreso')->sum('monto');
        $montoEgresos = $caja->movimientos()->where('tipo', 'egreso')->sum('monto');

        $montoEsperado = $caja->monto_apertura + $montoVentasEfectivo + $montoServiciosEfectivo + $montoIngresos - $montoEgresos;
        $diferencia = $request->monto_cierre_efectivo - $montoEsperado;

        $caja->update([
            'monto_cierre_efectivo' => $request->monto_cierre_efectivo,
            'monto_esperado_efectivo' => $montoEsperado,
            'diferencia' => $diferencia,
            'estado' => 'cerrada',
            'fecha_cierre' => now(),
            'observaciones' => $request->observaciones
        ]);

        $this->logActivity('CLOSE_CASH', "Turno de caja cerrado. Efectivo real: Bs{$caja->monto_cierre_efectivo}, Esperado: Bs{$montoEsperado}, Diferencia: Bs{$diferencia}", $caja->toArray());

        return redirect()->route('cajas.index')->with('success', 'Turno de caja cerrado y arqueado correctamente.');
    }

    public function storeMovimiento(Request $request, Caja $caja)
    {
        $request->validate([
            'tipo' => 'required|in:ingreso,egreso',
            'monto' => 'required|numeric|min:0.1',
            'concepto' => 'required|string|max:255'
        ]);

        if ($caja->estado !== 'abierta') {
            return back()->with('error', 'No se pueden agregar movimientos a una caja cerrada.');
        }

        $movimiento = MovimientoCaja::create([
            'caja_id' => $caja->id,
            'user_id' => auth()->id(),
            'tipo' => $request->tipo,
            'monto' => $request->monto,
            'concepto' => $request->concepto
        ]);

        $this->logActivity('CASH_MOVEMENT', "Movimiento de caja ({$request->tipo}): Bs{$request->monto} - {$request->concepto}", $movimiento->toArray());

        return back()->with('success', "Movimiento de {$request->tipo} registrado exitosamente.");
    }
}
