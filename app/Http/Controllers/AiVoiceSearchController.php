<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Traits\LogsActivity;

class AiVoiceSearchController extends Controller
{
    use LogsActivity;

    public function processVoice(Request $request)
    {
        $request->validate([
            'query' => 'required|string|max:500'
        ]);

        $userQuery = trim($request->input('query'));
        $lowerQuery = mb_strtolower($userQuery, 'UTF-8');
        $apiKey = config('services.gemini.api_key');

        // 1. Verificación de Seguridad en Lenguaje Natural (READ-ONLY Safety Check)
        $unsafeKeywords = ['borrar', 'eliminar', 'drop', 'truncate', 'update', 'destruir', 'alterar', 'quitar'];
        foreach ($unsafeKeywords as $badWord) {
            if (str_contains($lowerQuery, $badWord)) {
                return response()->json([
                    'success' => true,
                    'user_query' => $userQuery,
                    'data' => [
                        'is_safe' => false,
                        'target_module' => 'seguridad',
                        'redirect_url' => '#',
                        'extracted_search' => $userQuery,
                        'ai_summary' => "Por razones de seguridad, las búsquedas por voz con IA están restringidas exclusivamente a consultas de lectura (READ-ONLY). No se permiten modificaciones de datos."
                    ]
                ]);
            }
        }

        // 2. Intentar procesamiento con Google Gemini API
        if (!empty($apiKey)) {
            $modelsToTry = ['gemini-2.0-flash', 'gemini-2.0-flash-lite'];
            $systemPrompt = <<<PROMPT
Eres el Asistente IA de Búsqueda del sistema del Salón de Belleza ("Salón Anita").
Tu función es interpretar comandos de voz en lenguaje natural en español y traducirlos ÚNICAMENTE a filtros y consultas de LECTURA (READ-ONLY).

MÓDULOS DISPONIBLES EN EL SISTEMA:
1. `productos`: Catálogo e inventario. Ejemplo: `/productos?search=shampoo` o `/productos?stock=bajo`
2. `servicios`: Servicios de salón. Ejemplo: `/servicios?search=corte`
3. `citas`: Agenda de citas. Ejemplo: `/citas?search=Maria` o `/citas?estado=completada`
4. `ventas`: Historial de ventas. Ejemplo: `/ventas?search=Maria`
5. `clientes`: Directorio de clientes. Ejemplo: `/clientes?search=Juan`
6. `comisiones`: Comisiones de estilistas. Ejemplo: `/comisiones`
7. `alertas`: Alertas de inventario. Ejemplo: `/alertas`
8. `reportes`: Reportes administrativos. Ejemplo: `/reportes?rango=mes`

DEBES RESPONDER EXCLUSIVAMENTE EN FORMATO JSON VÁLIDO SIN MARKDOWN:
{
    "is_safe": true,
    "target_module": "nombre_del_modulo",
    "redirect_url": "/ruta_con_parametros",
    "extracted_search": "termino_clave",
    "ai_summary": "Explicación breve en español"
}
PROMPT;

            foreach ($modelsToTry as $model) {
                try {
                    $response = Http::withHeaders([
                        'Content-Type' => 'application/json',
                    ])->timeout(8)->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}", [
                        'contents' => [
                            [
                                'parts' => [
                                    ['text' => $systemPrompt],
                                    ['text' => "Comando de voz: \"{$userQuery}\""]
                                ]
                            ]
                        ],
                        'generationConfig' => [
                            'temperature' => 0.1,
                            'responseMimeType' => 'application/json'
                        ]
                    ]);

                    if ($response->successful()) {
                        $responseData = $response->json();
                        $rawContent = $responseData['candidates'][0]['content']['parts'][0]['text'] ?? '{}';
                        $cleanJson = preg_replace('/^```json\s*|\s*```$/i', '', trim($rawContent));
                        $aiResult = json_decode($cleanJson, true);

                        if ($aiResult && isset($aiResult['is_safe']) && isset($aiResult['redirect_url'])) {
                            if (auth()->check()) {
                                $this->logActivity('AI_VOICE_SEARCH', "Búsqueda Gemini ({$model}): '{$userQuery}'", [
                                    'query' => $userQuery,
                                    'ai_result' => $aiResult
                                ]);
                            }

                            return response()->json([
                                'success' => true,
                                'user_query' => $userQuery,
                                'data' => $aiResult
                            ]);
                        }
                    }
                } catch (\Exception $e) {
                    Log::warning("Gemini API ({$model}) exception: " . $e->getMessage());
                }
            }
        }

        // 3. Parser Inteligente Local Híbrido (Respaldo Inmediato si Gemini excede cuota o falla la API)
        $aiResult = $this->localIntelligentSearch($userQuery, $lowerQuery);

        if (auth()->check()) {
            $this->logActivity('AI_VOICE_SEARCH', "Búsqueda Inteligente Híbrida: '{$userQuery}'", [
                'query' => $userQuery,
                'ai_result' => $aiResult
            ]);
        }

        return response()->json([
            'success' => true,
            'user_query' => $userQuery,
            'data' => $aiResult
        ]);
    }

    /**
     * Motor inteligente local de respaldo para interpretar consultas sin depender exclusivamente de cuotas externas.
     */
    private function localIntelligentSearch(string $originalQuery, string $lowerQuery): array
    {
        // Palabras de módulos
        if (str_contains($lowerQuery, 'cita') || str_contains($lowerQuery, 'agenda') || str_contains($lowerQuery, 'reserva')) {
            $estado = null;
            if (str_contains($lowerQuery, 'completad')) $estado = 'completada';
            elseif (str_contains($lowerQuery, 'pendiente')) $estado = 'pendiente';
            elseif (str_contains($lowerQuery, 'confirmad')) $estado = 'confirmada';

            $cleanTerm = trim(str_replace(['citas', 'cita', 'agenda', 'reservas', 'reserva', 'completadas', 'pendientes', 'de'], '', $lowerQuery));

            if ($estado) {
                return [
                    'is_safe' => true,
                    'target_module' => 'citas',
                    'redirect_url' => route('citas.index', ['estado' => $estado]),
                    'extracted_search' => $estado,
                    'ai_summary' => "Filtrando citas con estado '{$estado}'."
                ];
            }

            return [
                'is_safe' => true,
                'target_module' => 'citas',
                'redirect_url' => route('citas.index', ['search' => $cleanTerm ?: $originalQuery]),
                'extracted_search' => $cleanTerm ?: $originalQuery,
                'ai_summary' => "Filtrando citas para '" . ($cleanTerm ?: $originalQuery) . "'."
            ];
        }

        if (str_contains($lowerQuery, 'cliente') || str_contains($lowerQuery, 'directorio')) {
            $cleanTerm = trim(str_replace(['clientes', 'cliente', 'directorio', 'buscar', 'llamados', 'llamada'], '', $lowerQuery));
            return [
                'is_safe' => true,
                'target_module' => 'clientes',
                'redirect_url' => route('clientes.index', ['search' => $cleanTerm ?: $originalQuery]),
                'extracted_search' => $cleanTerm ?: $originalQuery,
                'ai_summary' => "Buscando cliente '" . ($cleanTerm ?: $originalQuery) . "' en el directorio."
            ];
        }

        if (str_contains($lowerQuery, 'venta') || str_contains($lowerQuery, 'factura') || str_contains($lowerQuery, 'cobro')) {
            $cleanTerm = trim(str_replace(['ventas', 'venta', 'facturas', 'factura', 'cobros', 'buscar'], '', $lowerQuery));
            return [
                'is_safe' => true,
                'target_module' => 'ventas',
                'redirect_url' => route('ventas.index', ['search' => $cleanTerm ?: $originalQuery]),
                'extracted_search' => $cleanTerm ?: $originalQuery,
                'ai_summary' => "Filtrando historial de ventas para '" . ($cleanTerm ?: $originalQuery) . "'."
            ];
        }

        if (str_contains($lowerQuery, 'comision') || str_contains($lowerQuery, 'pago estilista')) {
            return [
                'is_safe' => true,
                'target_module' => 'comisiones',
                'redirect_url' => route('comisiones.index'),
                'extracted_search' => 'comisiones',
                'ai_summary' => "Consultando panel de comisiones de estilistas."
            ];
        }

        if (str_contains($lowerQuery, 'alerta') || str_contains($lowerQuery, 'stock bajo') || str_contains($lowerQuery, 'critico')) {
            return [
                'is_safe' => true,
                'target_module' => 'alertas',
                'redirect_url' => route('alertas.index'),
                'extracted_search' => 'stock_bajo',
                'ai_summary' => "Consultando centro de alertas de stock mínimo de productos."
            ];
        }

        if (str_contains($lowerQuery, 'reporte') || str_contains($lowerQuery, 'estadistica') || str_contains($lowerQuery, 'ingreso')) {
            return [
                'is_safe' => true,
                'target_module' => 'reportes',
                'redirect_url' => route('reportes.index'),
                'extracted_search' => 'reportes',
                'ai_summary' => "Generando reporte ejecutivo de ingresos y servicios."
            ];
        }

        if (str_contains($lowerQuery, 'caja') || str_contains($lowerQuery, 'arqueo') || str_contains($lowerQuery, 'gastos')) {
            return [
                'is_safe' => true,
                'target_module' => 'cajas',
                'redirect_url' => route('cajas.index'),
                'extracted_search' => 'cajas',
                'ai_summary' => "Consultando módulo de caja chica y arqueo diario."
            ];
        }

        if (str_contains($lowerQuery, 'servicio') || str_contains($lowerQuery, 'corte') || str_contains($lowerQuery, 'manicura') || str_contains($lowerQuery, 'tinte') || str_contains($lowerQuery, 'peinado')) {
            $cleanTerm = trim(str_replace(['servicios', 'servicio', 'buscar'], '', $lowerQuery));
            return [
                'is_safe' => true,
                'target_module' => 'servicios',
                'redirect_url' => route('servicios.index', ['search' => $cleanTerm ?: $originalQuery]),
                'extracted_search' => $cleanTerm ?: $originalQuery,
                'ai_summary' => "Buscando servicio de salón '" . ($cleanTerm ?: $originalQuery) . "'."
            ];
        }

        // Predeterminado: Búsqueda en catálogo de productos
        $cleanTerm = trim(str_replace(['buscar', 'productos', 'producto', 'con'], '', $lowerQuery));
        return [
            'is_safe' => true,
            'target_module' => 'productos',
            'redirect_url' => route('productos.index', ['search' => $cleanTerm ?: $originalQuery]),
            'extracted_search' => $cleanTerm ?: $originalQuery,
            'ai_summary' => "Buscando en el catálogo de productos por '" . ($cleanTerm ?: $originalQuery) . "'."
        ];
    }
}
