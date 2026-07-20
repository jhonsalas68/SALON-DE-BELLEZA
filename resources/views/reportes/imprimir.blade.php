<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Ejecutivo - Salón Anita</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800;900&display=swap');
        body { font-family: 'Outfit', sans-serif; }
        @media print {
            .no-print { display: none !important; }
            body { background: white; color: black; }
            .page-break { page-break-before: always; }
        }
    </style>
</head>
<body class="bg-gray-100 text-gray-900 p-8 min-h-screen">

    <div class="max-w-4xl mx-auto bg-white p-8 rounded-2xl shadow-sm border border-gray-200">
        
        <!-- Header del Documento Imprimible -->
        <div class="flex items-center justify-between border-b-2 border-rose-500 pb-6 mb-6">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 bg-rose-600 rounded-xl flex items-center justify-center text-white text-xl shadow-sm">
                    <i class="fas fa-spa"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-black uppercase tracking-tight text-gray-900">Salón Anita</h1>
                    <p class="text-xs text-rose-600 font-bold uppercase tracking-widest">Reporte Administrativo Ejecutivo (CU20)</p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Periodo Evaluado</p>
                <p class="text-sm font-black text-gray-800">{{ $fechaInicio->format('d/m/Y') }} al {{ $fechaFin->format('d/m/Y') }}</p>
                <p class="text-[10px] text-gray-400 mt-1">Generado: {{ now()->format('d/m/Y H:i') }}</p>
            </div>
        </div>

        <!-- Botones de Acción (Se ocultan al imprimir) -->
        <div class="no-print mb-6 flex justify-end space-x-3">
            <button onclick="window.print()" class="px-6 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs rounded-xl shadow-md transition-all flex items-center space-x-2">
                <i class="fas fa-print"></i>
                <span>Imprimir / Guardar como PDF</span>
            </button>
            <button onclick="window.close()" class="px-4 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold text-xs rounded-xl transition-all">
                Cerrar
            </button>
        </div>

        <!-- Resumen de Métricas KPI -->
        <div class="grid grid-cols-4 gap-4 mb-8 text-center">
            <div class="p-4 bg-gray-50 rounded-xl border border-gray-200">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Total Ingresos</p>
                <p class="text-lg font-black text-gray-900 mt-1">Bs {{ number_format($totalIngresoVentas + $totalIngresoServicios, 2) }}</p>
            </div>
            <div class="p-4 bg-gray-50 rounded-xl border border-gray-200">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Venta Productos</p>
                <p class="text-lg font-black text-emerald-600 mt-1">Bs {{ number_format($totalIngresoVentas, 2) }}</p>
            </div>
            <div class="p-4 bg-gray-50 rounded-xl border border-gray-200">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Servicios Realizados</p>
                <p class="text-lg font-black text-rose-600 mt-1">Bs {{ number_format($totalIngresoServicios, 2) }}</p>
            </div>
            <div class="p-4 bg-gray-50 rounded-xl border border-gray-200">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Comisiones Totales</p>
                <p class="text-lg font-black text-indigo-600 mt-1">Bs {{ number_format($totalComisionesGeneradas, 2) }}</p>
            </div>
        </div>

        <!-- Rendimiento Estilistas -->
        <div class="mb-8">
            <h2 class="text-sm font-black uppercase tracking-wider text-gray-800 border-b border-gray-200 pb-2 mb-3">
                1. Rendimiento y Comisiones de Estilistas (CU8 & CU18)
            </h2>
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-gray-100 font-extrabold uppercase text-gray-600">
                        <th class="py-2 px-3">Estilista</th>
                        <th class="py-2 px-3 text-center">Citas Completadas</th>
                        <th class="py-2 px-3 text-right">Ingreso Generado</th>
                        <th class="py-2 px-3 text-right">Comisión Calculada</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 font-medium">
                    @forelse($rendimientoEstilistas as $estilista)
                    <tr>
                        <td class="py-2 px-3 font-bold text-gray-900">{{ $estilista->name }}</td>
                        <td class="py-2 px-3 text-center">{{ $estilista->citas_count }}</td>
                        <td class="py-2 px-3 text-right">Bs {{ number_format($estilista->total_ingresos_generados ?: 0, 2) }}</td>
                        <td class="py-2 px-3 text-right font-bold text-rose-600">Bs {{ number_format($estilista->total_comisiones ?: 0, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-3 text-center text-gray-400">Sin datos de comisiones en el periodo.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Top Productos -->
        <div class="mb-8">
            <h2 class="text-sm font-black uppercase tracking-wider text-gray-800 border-b border-gray-200 pb-2 mb-3">
                2. Productos Más Vendidos (CU22)
            </h2>
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-gray-100 font-extrabold uppercase text-gray-600">
                        <th class="py-2 px-3">Producto</th>
                        <th class="py-2 px-3 text-center">Unidades Vendidas</th>
                        <th class="py-2 px-3 text-right">Subtotal Ingresos</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 font-medium">
                    @forelse($topProductos as $item)
                    <tr>
                        <td class="py-2 px-3 font-bold text-gray-900">{{ $item->producto->nombre ?? 'N/A' }}</td>
                        <td class="py-2 px-3 text-center">{{ $item->total_vendido }} un.</td>
                        <td class="py-2 px-3 text-right font-bold">Bs {{ number_format($item->total_ingreso, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="py-3 text-center text-gray-400">Sin datos de productos en el periodo.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Alertas de Stock Bajo -->
        <div>
            <h2 class="text-sm font-black uppercase tracking-wider text-gray-800 border-b border-gray-200 pb-2 mb-3">
                3. Alertas y Estado Crítico de Inventario (CU15)
            </h2>
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-gray-100 font-extrabold uppercase text-gray-600">
                        <th class="py-2 px-3">Producto</th>
                        <th class="py-2 px-3 text-center">Stock Actual</th>
                        <th class="py-2 px-3 text-center">Stock Mínimo Exigido</th>
                        <th class="py-2 px-3 text-center">Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 font-medium">
                    @forelse($productosStockBajo as $prod)
                    <tr>
                        <td class="py-2 px-3 font-bold text-gray-900">{{ $prod->nombre }}</td>
                        <td class="py-2 px-3 text-center font-bold text-rose-600">{{ $prod->stock }}</td>
                        <td class="py-2 px-3 text-center">{{ $prod->stock_minimo }}</td>
                        <td class="py-2 px-3 text-center text-rose-600 font-bold uppercase">Stock Bajo</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-3 text-center text-emerald-600 font-bold">Nivel de stock óptimo en todos los productos.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Firma de Autorización -->
        <div class="mt-16 pt-8 border-t border-gray-200 flex justify-between items-end text-xs text-gray-500">
            <div>
                <p>Salón Anita - Sistema de Gestión Administrativa</p>
                <p class="text-[10px]">Doc ID: REP-{{ now()->format('YmdHis') }}</p>
            </div>
            <div class="text-center w-48">
                <div class="border-b border-gray-400 mb-1"></div>
                <p class="font-bold text-gray-800">Firma Administradora</p>
            </div>
        </div>

    </div>

</body>
</html>
