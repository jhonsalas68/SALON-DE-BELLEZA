<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Servicio;
use App\Models\Producto;
use App\Models\Cita;
use App\Models\Venta;
use App\Models\Comision;
use App\Models\Caja;
use App\Models\Promocion;
use App\Models\Valoracion;
use App\Models\ActivityLog;

class ReporteController extends Controller
{
    public function export(Request $request, string $modulo, string $format)
    {
        $data = $this->getModuleData($modulo, $request);
        
        if ($format === 'excel' || $format === 'csv') {
            return $this->exportExcel($modulo, $data);
        }

        if ($format === 'pdf') {
            return $this->exportPdfView($modulo, $data);
        }

        abort(404, 'Formato no soportado.');
    }

    private function getModuleData(string $modulo, Request $request): array
    {
        switch ($modulo) {
            case 'clientes':
                $headers = ['ID', 'Nombre', 'Email', 'Teléfono', 'Puntos Fidelidad', 'Fecha Registro'];
                $rows = User::whereHas('role', function($q) { $q->where('slug', 'cliente'); })
                    ->orderBy('created_at', 'desc')
                    ->get()
                    ->map(fn($u) => [
                        $u->id,
                        $u->name ?? 'N/A',
                        $u->email,
                        $u->telefono ?? 'N/A',
                        $u->puntos ?? 0,
                        $u->created_at->format('d/m/Y H:i')
                    ])->toArray();
                $title = 'Reporte General de Clientes';
                break;

            case 'servicios':
                $headers = ['ID', 'Nombre', 'Descripción', 'Precio (Bs)', 'Duración', 'Estado'];
                $rows = Servicio::get()->map(fn($s) => [
                    $s->id,
                    $s->nombre,
                    $s->descripcion ?? 'N/A',
                    number_format($s->precio, 2),
                    $s->duracion_minutos . ' min',
                    $s->activo ? 'Activo' : 'Inactivo'
                ])->toArray();
                $title = 'Catálogo de Servicios';
                break;

            case 'productos':
                $headers = ['ID', 'Código', 'Nombre', 'Precio Compra (Bs)', 'Precio Venta (Bs)', 'Stock', 'Stock Mínimo', 'Vencimiento'];
                $rows = Producto::get()->map(fn($p) => [
                    $p->id,
                    $p->codigo ?? 'N/A',
                    $p->nombre,
                    number_format($p->precio_compra, 2),
                    number_format($p->precio_venta, 2),
                    $p->stock,
                    $p->stock_minimo,
                    $p->fecha_caducidad ? \Carbon\Carbon::parse($p->fecha_caducidad)->format('d/m/Y') : 'N/A'
                ])->toArray();
                $title = 'Inventario de Productos';
                break;

            case 'citas':
                $headers = ['ID', 'Fecha', 'Hora', 'Cliente', 'Servicio', 'Estilista', 'Estado', 'Precio (Bs)'];
                $rows = Cita::with(['cliente', 'servicio', 'estilista'])->orderBy('fecha', 'desc')->get()->map(fn($c) => [
                    $c->id,
                    $c->fecha,
                    $c->hora,
                    $c->cliente->name ?? 'Casual',
                    $c->servicio->nombre ?? 'N/A',
                    $c->estilista->name ?? 'No asignado',
                    strtoupper($c->estado),
                    number_format($c->servicio->precio ?? 0, 2)
                ])->toArray();
                $title = 'Reporte General de Citas y Reservas';
                break;

            case 'ventas':
                $headers = ['ID', 'Fecha Venta', 'Cliente', 'Vendedor', 'Subtotal (Bs)', 'Descuento (Bs)', 'Total (Bs)', 'Método Pago', 'Estado Pago'];
                $rows = Venta::with(['cliente', 'vendedor'])->orderBy('fecha_venta', 'desc')->get()->map(fn($v) => [
                    $v->id,
                    $v->fecha_venta ? \Carbon\Carbon::parse($v->fecha_venta)->format('d/m/Y H:i') : 'N/A',
                    $v->cliente_nombre ?: ($v->cliente->name ?? 'Casual'),
                    $v->vendedor->name ?? 'Sistema',
                    number_format($v->subtotal, 2),
                    number_format($v->descuento, 2),
                    number_format($v->total, 2),
                    strtoupper($v->metodo_pago),
                    strtoupper($v->estado_pago)
                ])->toArray();
                $title = 'Reporte de Ventas e Ingresos';
                break;

            case 'comisiones':
                $headers = ['ID', 'Fecha', 'Estilista', 'Cita ID', 'Servicio', 'Precio Servicio (Bs)', 'Porcentaje (%)', 'Monto Comisión (Bs)', 'Estado'];
                $rows = Comision::with(['estilista', 'cita.servicio'])->orderBy('fecha_generacion', 'desc')->get()->map(fn($co) => [
                    $co->id,
                    \Carbon\Carbon::parse($co->fecha_generacion)->format('d/m/Y'),
                    $co->estilista->name ?? 'N/A',
                    $co->cita_id,
                    $co->cita->servicio->nombre ?? 'Servicio',
                    number_format($co->precio_servicio, 2),
                    $co->porcentaje_aplicado . '%',
                    number_format($co->monto_comision, 2),
                    strtoupper($co->estado_pago)
                ])->toArray();
                $title = 'Reporte de Comisiones de Estilistas';
                break;

            case 'cajas':
                $headers = ['ID', 'Apertura', 'Cierre', 'Cajero', 'Apertura (Bs)', 'Ventas (Bs)', 'Servicios (Bs)', 'Esperado (Bs)', 'Cierre Real (Bs)', 'Diferencia (Bs)', 'Estado'];
                $rows = Caja::with('user')->orderBy('fecha_apertura', 'desc')->get()->map(fn($cj) => [
                    $cj->id,
                    \Carbon\Carbon::parse($cj->fecha_apertura)->format('d/m/Y H:i'),
                    $cj->fecha_cierre ? \Carbon\Carbon::parse($cj->fecha_cierre)->format('d/m/Y H:i') : 'Abierta',
                    $cj->user->name ?? 'N/A',
                    number_format($cj->monto_apertura, 2),
                    number_format($cj->monto_ventas_efectivo ?? 0, 2),
                    number_format($cj->monto_servicios_efectivo ?? 0, 2),
                    number_format($cj->monto_esperado_efectivo ?? 0, 2),
                    number_format($cj->monto_cierre_efectivo ?? 0, 2),
                    number_format($cj->diferencia ?? 0, 2),
                    strtoupper($cj->estado)
                ])->toArray();
                $title = 'Reporte de Arqueos de Caja Chica';
                break;

            case 'promociones':
                $headers = ['ID', 'Título', 'Item Aplicable', 'Descuento (%)', 'Fecha Inicio', 'Fecha Fin', 'Estado'];
                $rows = Promocion::with(['servicio', 'producto'])->get()->map(fn($pr) => [
                    $pr->id,
                    $pr->titulo,
                    $pr->servicio->nombre ?? ($pr->producto->nombre ?? 'General'),
                    $pr->descuento_porcentaje . '%',
                    $pr->fecha_inicio,
                    $pr->fecha_fin,
                    $pr->activo ? 'ACTIVA' : 'INACTIVA'
                ])->toArray();
                $title = 'Reporte de Promociones y Descuentos';
                break;

            case 'valoraciones':
                $headers = ['ID', 'Fecha', 'Cliente', 'Estilista', 'Cita ID', 'Calificación', 'Comentario'];
                $rows = Valoracion::with(['cliente', 'estilista'])->orderBy('fecha', 'desc')->get()->map(fn($val) => [
                    $val->id,
                    \Carbon\Carbon::parse($val->fecha)->format('d/m/Y H:i'),
                    $val->cliente->name ?? 'Anónimo',
                    $val->estilista->name ?? 'General',
                    $val->cita_id ?? 'N/A',
                    $val->estrellas . ' ★',
                    $val->comentario ?? 'Sin comentario'
                ])->toArray();
                $title = 'Reporte de Valoraciones y Opiniones NPS';
                break;

            case 'activity-logs':
                $headers = ['ID', 'Fecha / Hora', 'Usuario', 'Acción', 'Descripción', 'IP'];
                $rows = ActivityLog::with('user')->orderBy('created_at', 'desc')->take(200)->get()->map(fn($log) => [
                    $log->id,
                    $log->created_at->format('d/m/Y H:i:s'),
                    $log->user->name ?? 'Sistema',
                    $log->action,
                    $log->description,
                    $log->ip_address ?? 'N/A'
                ])->toArray();
                $title = 'Reporte de Bitácora de Auditoría';
                break;

            default:
                abort(404, 'Módulo no encontrado');
        }

        return [
            'headers' => $headers,
            'rows' => $rows,
            'title' => $title,
            'modulo' => $modulo,
            'fecha' => now()->format('d/m/Y H:i:s')
        ];
    }

    private function exportExcel(string $modulo, array $data)
    {
        $filename = "reporte_{$modulo}_" . now()->format('Y-m-d_His') . ".csv";

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            
            // UTF-8 BOM for Excel to handle special characters correctly
            fputs($file, "\xEF\xBB\xBF");

            // Header row
            fputcsv($file, $data['headers']);

            // Data rows
            foreach ($data['rows'] as $row) {
                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, [
            "Content-Type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename={$filename}",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ]);
    }

    private function exportPdfView(string $modulo, array $data)
    {
        return view('reports.print', compact('data'));
    }
}
