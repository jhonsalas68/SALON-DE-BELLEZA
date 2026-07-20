<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\VentaDetalle;
use App\Models\Cita;
use App\Models\Comision;
use App\Models\Producto;
use App\Models\User;
use App\Models\Alerta;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Traits\LogsActivity;

class ReporteController extends Controller
{
    use LogsActivity;

    public function index(Request $request)
    {
        $rango = $request->get('rango', 'mes'); // hoy, semana, mes, personalizado
        $fechaInicio = null;
        $fechaFin = null;

        if ($rango === 'hoy') {
            $fechaInicio = Carbon::today()->startOfDay();
            $fechaFin = Carbon::today()->endOfDay();
        } elseif ($rango === 'semana') {
            $fechaInicio = Carbon::now()->startOfWeek();
            $fechaFin = Carbon::now()->endOfWeek();
        } elseif ($rango === 'personalizado' && $request->filled('fecha_inicio') && $request->filled('fecha_fin')) {
            $fechaInicio = Carbon::parse($request->fecha_inicio)->startOfDay();
            $fechaFin = Carbon::parse($request->fecha_fin)->endOfDay();
        } else {
            // Predeterminado: Este Mes
            $rango = 'mes';
            $fechaInicio = Carbon::now()->startOfMonth();
            $fechaFin = Carbon::now()->endOfMonth();
        }

        // 1. Métricas de Ventas de Productos
        $ventasQuery = Venta::whereBetween('fecha_venta', [$fechaInicio, $fechaFin])
            ->where('estado_pago', 'completado');

        $totalIngresoVentas = (clone $ventasQuery)->sum('total');
        $totalCantidadVentas = (clone $ventasQuery)->count();

        // Top Productos Vendidos en el Periodo
        $topProductos = VentaDetalle::selectRaw('producto_id, SUM(cantidad) as total_vendido, SUM(subtotal) as total_ingreso')
            ->whereHas('venta', function($q) use ($fechaInicio, $fechaFin) {
                $q->whereBetween('fecha_venta', [$fechaInicio, $fechaFin])
                  ->where('estado_pago', 'completado');
            })
            ->groupBy('producto_id')
            ->with('producto')
            ->orderBy('total_vendido', 'desc')
            ->take(5)
            ->get();

        // 2. Métricas de Servicios Realizados (Citas Completadas)
        $citasQuery = Cita::whereBetween('fecha', [$fechaInicio->format('Y-m-d'), $fechaFin->format('Y-m-d')])
            ->where('estado', 'completada');

        $totalServiciosCompletados = (clone $citasQuery)->count();

        // 3. Métricas de Comisiones de Estilistas
        $comisionesQuery = Comision::whereBetween('fecha_calculo', [$fechaInicio, $fechaFin]);

        $totalComisionesGeneradas = (clone $comisionesQuery)->sum('monto_comision');
        $totalComisionesPagadas = (clone $comisionesQuery)->where('estado', 'pagado')->sum('monto_comision');
        $totalComisionesPendientes = (clone $comisionesQuery)->where('estado', 'pendiente')->sum('monto_comision');
        $totalIngresoServicios = (clone $comisionesQuery)->sum('monto_servicio');

        // Rendimiento por Estilista
        $rendimientoEstilistas = User::whereHas('role', function($q) {
                $q->where('slug', 'estilista');
            })
            ->withCount(['citas' => function($q) use ($fechaInicio, $fechaFin) {
                $q->whereBetween('fecha', [$fechaInicio->format('Y-m-d'), $fechaFin->format('Y-m-d')])
                  ->where('estado', 'completada');
            }])
            ->withSum(['comisiones as total_comisiones' => function($q) use ($fechaInicio, $fechaFin) {
                $q->whereBetween('fecha_calculo', [$fechaInicio, $fechaFin]);
            }], 'monto_comision')
            ->withSum(['comisiones as total_ingresos_generados' => function($q) use ($fechaInicio, $fechaFin) {
                $q->whereBetween('fecha_calculo', [$fechaInicio, $fechaFin]);
            }], 'monto_servicio')
            ->get();

        // 4. Alertas de Stock Bajo Actuales
        $productosStockBajo = Producto::whereColumn('stock', '<=', 'stock_minimo')->get();

        if (auth()->check()) {
            $this->logActivity('VIEW_REPORTS', "Consulta de reportes administrativos. Rango: {$rango}", [
                'rango' => $rango,
                'fecha_inicio' => $fechaInicio->toDateTimeString(),
                'fecha_fin' => $fechaFin->toDateTimeString()
            ]);
        }

        return view('reportes.index', compact(
            'rango',
            'fechaInicio',
            'fechaFin',
            'totalIngresoVentas',
            'totalCantidadVentas',
            'topProductos',
            'totalServiciosCompletados',
            'totalIngresoServicios',
            'totalComisionesGeneradas',
            'totalComisionesPagadas',
            'totalComisionesPendientes',
            'rendimientoEstilistas',
            'productosStockBajo'
        ));
    }

    public function imprimir(Request $request)
    {
        // Reutilizar la lógica de obtención de datos para la vista de impresión limpia
        $response = $this->index($request);
        $data = $response->getData();

        return view('reportes.imprimir', (array) $data);
    }
}
