<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $data['title'] }} - Salon Anita</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; font-size: 11px; }
            .print-container { padding: 0 !important; box-shadow: none !important; border: none !important; }
            table { page-break-inside: auto; }
            tr { page-break-inside: avoid; page-break-after: auto; }
        }
    </style>
</head>
<body class="bg-gray-100 text-gray-800 p-4 md:p-8 font-sans">

    <!-- Barra Superior de Acciones (Oculta al imprimir) -->
    <div class="max-w-6xl mx-auto mb-6 flex justify-between items-center no-print">
        <button onclick="window.history.back()" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-xl font-bold text-sm flex items-center gap-2 transition-all">
            <i class="fas fa-arrow-left"></i> Volver al Sistema
        </button>

        <div class="flex items-center space-x-3">
            <a href="{{ route('reports.export', ['modulo' => $data['modulo'], 'format' => 'excel']) }}" class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2 rounded-xl font-bold text-sm flex items-center gap-2 shadow-md transition-all">
                <i class="fas fa-file-excel"></i> Descargar Excel (.csv)
            </a>
            <button onclick="window.print()" class="bg-rose-500 hover:bg-rose-600 text-white px-5 py-2 rounded-xl font-bold text-sm flex items-center gap-2 shadow-md transition-all">
                <i class="fas fa-file-pdf"></i> Guardar como PDF / Imprimir
            </button>
        </div>
    </div>

    <!-- Hoja del Reporte -->
    <div class="max-w-6xl mx-auto bg-white p-8 md:p-12 rounded-3xl shadow-lg border border-gray-200 print-container">
        
        <!-- Header Reporte -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center pb-6 mb-6 border-b-2 border-rose-500 gap-4">
            <div>
                <div class="flex items-center gap-3 mb-1">
                    <div class="w-10 h-10 bg-rose-500 text-white rounded-xl flex items-center justify-center font-black text-xl shadow-md">
                        A
                    </div>
                    <div>
                        <h1 class="text-2xl font-black text-gray-900 tracking-tight">SALON ANITA</h1>
                        <p class="text-xs text-rose-600 font-bold uppercase tracking-widest">Sistema de Gestión Integrado</p>
                    </div>
                </div>
            </div>

            <div class="text-left md:text-right">
                <h2 class="text-xl font-black text-gray-800">{{ $data['title'] }}</h2>
                <p class="text-xs text-gray-500 font-medium mt-1">Generado: <span class="font-bold text-gray-700">{{ $data['fecha'] }}</span></p>
                <p class="text-xs text-gray-500 font-medium">Generado por: <span class="font-bold text-gray-700">{{ auth()->user()->name ?? 'Usuario Sistema' }}</span></p>
            </div>
        </div>

        <!-- Tabla de Datos -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-rose-50 border-y border-rose-200">
                        @foreach($data['headers'] as $header)
                            <th class="py-3 px-3 font-extrabold text-rose-900 uppercase tracking-wider text-[11px]">{{ $header }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($data['rows'] as $index => $row)
                        <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50/50' }} hover:bg-rose-50/30 transition-colors">
                            @foreach($row as $cell)
                                <td class="py-3 px-3 font-medium text-gray-700 leading-snug">{{ $cell }}</td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($data['headers']) }}" class="py-8 text-center text-gray-400 font-medium">
                                No hay registros disponibles para este reporte.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pie de Página del Reporte -->
        <div class="mt-12 pt-6 border-t border-gray-200 flex flex-col md:flex-row justify-between items-center text-xs text-gray-400 font-medium">
            <p>© {{ date('Y') }} Salon de Belleza Anita. Todos los derechos reservados.</p>
            <p>Total de registros exportados: <strong class="text-gray-700">{{ count($data['rows']) }}</strong></p>
        </div>
    </div>

    <script>
        // Auto activar diálogo de impresión si se desea
        window.addEventListener('DOMContentLoaded', () => {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('autoprint') === '1') {
                setTimeout(() => window.print(), 500);
            }
        });
    </script>
</body>
</html>
