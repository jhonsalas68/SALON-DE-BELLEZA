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
                        'ai_summary' => "Por razones de seguridad, las búsquedas por voz con IA están restringidas exclusivamente a consultas de lectura (READ-ONLY)."
                    ]
                ]);
            }
        }

        // 2. Intentar procesamiento con Google Gemini API
        if (!empty($apiKey)) {
            $modelsToTry = ['gemini-2.0-flash', 'gemini-2.0-flash-lite'];
            $systemPrompt = <<<PROMPT
Eres el Asistente de Voz Inteligente del Salón de Belleza ("Salón Anita").
Tu objetivo es traducir comandos hablados en español a URLs de consulta y filtrado READ-ONLY.

REGLAS DE RUTAS Y FILTROS:
- "stock bajo", "estado de stock bajo", "poco stock", "critico" -> `/productos?stock_status=bajo`
- "productos vencidos", "proximos a vencer" -> `/productos?vencimiento=proximo`
- "acondicionador", "shampoo", "nombre de producto" -> `/productos?search=NOMBRE`
- "citas completadas", "citas pendientes" -> `/citas?estado=ESTADO`
- "citas de X", "agenda" -> `/citas?search=BUSQUEDA`
- "reportes", "estadisticas", "ingresos" -> `/reportes`
- "caja", "caja chica", "arqueo" -> `/cajas`
- "valoraciones", "encuestas", "estrellas" -> `/valoraciones`
- "clientes", "directorio" -> `/clientes?search=BUSQUEDA`
- "ventas", "facturas" -> `/ventas?search=BUSQUEDA`

Responde EXCLUSIVAMENTE en JSON puro sin bloques de código:
{
    "is_safe": true,
    "target_module": "nombre_modulo",
    "redirect_url": "/ruta_con_parametros",
    "extracted_search": "termino",
    "ai_summary": "Explicacion en español"
}
PROMPT;

            foreach ($modelsToTry as $model) {
                try {
                    $response = Http::withHeaders([
                        'Content-Type' => 'application/json',
                    ])->timeout(6)->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}", [
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

        // 3. Motor Inteligente de Respaldo Híbrido Avanzado
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
     * Motor de interpretación local avanzado para procesar consultas habladas sin fallos.
     */
    private function localIntelligentSearch(string $originalQuery, string $lowerQuery): array
    {
        // 1. Filtros Específicos de Stock y Productos
        if (str_contains($lowerQuery, 'stock bajo') || str_contains($lowerQuery, 'estado de stock') || str_contains($lowerQuery, 'poco stock') || str_contains($lowerQuery, 'agotad') || str_contains($lowerQuery, 'critico')) {
            return [
                'is_safe' => true,
                'target_module' => 'productos',
                'redirect_url' => route('productos.index', ['stock_status' => 'bajo']),
                'extracted_search' => 'stock_bajo',
                'ai_summary' => "Filtrando catálogo por productos en estado de stock bajo o crítico."
            ];
        }

        if (str_contains($lowerQuery, 'vencid') || str_contains($lowerQuery, 'proximo a vencer') || str_contains($lowerQuery, 'caducad')) {
            return [
                'is_safe' => true,
                'target_module' => 'productos',
                'redirect_url' => route('productos.index', ['vencimiento' => 'proximo']),
                'extracted_search' => 'proximo_vencer',
                'ai_summary' => "Filtrando productos próximos a vencer."
            ];
        }

        // 2. Reportes Administrativos
        if (str_contains($lowerQuery, 'reporte') || str_contains($lowerQuery, 'estadistica') || str_contains($lowerQuery, 'resumen ejecutivo') || str_contains($lowerQuery, 'ingreso')) {
            return [
                'is_safe' => true,
                'target_module' => 'reportes',
                'redirect_url' => route('reportes.index'),
                'extracted_search' => 'reportes',
                'ai_summary' => "Generando reporte ejecutivo del salón."
            ];
        }

        // 3. Arqueo y Caja Chica
        if (str_contains($lowerQuery, 'caja') || str_contains($lowerQuery, 'arqueo') || str_contains($lowerQuery, 'gasto')) {
            return [
                'is_safe' => true,
                'target_module' => 'cajas',
                'redirect_url' => route('cajas.index'),
                'extracted_search' => 'cajas',
                'ai_summary' => "Consultando el módulo de caja chica y arqueos diarios."
            ];
        }

        // 4. Valoraciones y NPS
        if (str_contains($lowerQuery, 'valoracio') || str_contains($lowerQuery, 'califica') || str_contains($lowerQuery, 'nps') || str_contains($lowerQuery, 'estrella')) {
            return [
                'is_safe' => true,
                'target_module' => 'valoraciones',
                'redirect_url' => route('valoraciones.index'),
                'extracted_search' => 'valoraciones',
                'ai_summary' => "Consultando las valoraciones y calificaciones del personal."
            ];
        }

        // 5. Citas y Agenda
        if (str_contains($lowerQuery, 'cita') || str_contains($lowerQuery, 'agenda') || str_contains($lowerQuery, 'reserva')) {
            $estado = null;
            if (str_contains($lowerQuery, 'completad')) $estado = 'completada';
            elseif (str_contains($lowerQuery, 'pendiente')) $estado = 'pendiente';
            elseif (str_contains($lowerQuery, 'confirmad')) $estado = 'confirmada';

            $cleanTerm = $this->cleanFillerWords($lowerQuery, ['citas', 'cita', 'agenda', 'reservas', 'reserva', 'completadas', 'pendientes', 'confirmadas']);

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
                'ai_summary' => "Filtrando agenda de citas para '" . ($cleanTerm ?: $originalQuery) . "'."
            ];
        }

        // 6. Clientes
        if (str_contains($lowerQuery, 'cliente') || str_contains($lowerQuery, 'directorio')) {
            $cleanTerm = $this->cleanFillerWords($lowerQuery, ['clientes', 'cliente', 'directorio', 'llamados', 'llamada']);
            return [
                'is_safe' => true,
                'target_module' => 'clientes',
                'redirect_url' => route('clientes.index', ['search' => $cleanTerm ?: $originalQuery]),
                'extracted_search' => $cleanTerm ?: $originalQuery,
                'ai_summary' => "Buscando cliente '" . ($cleanTerm ?: $originalQuery) . "'."
            ];
        }

        // 7. Ventas
        if (str_contains($lowerQuery, 'venta') || str_contains($lowerQuery, 'factura') || str_contains($lowerQuery, 'cobro')) {
            $cleanTerm = $this->cleanFillerWords($lowerQuery, ['ventas', 'venta', 'facturas', 'factura', 'cobros']);
            return [
                'is_safe' => true,
                'target_module' => 'ventas',
                'redirect_url' => route('ventas.index', ['search' => $cleanTerm ?: $originalQuery]),
                'extracted_search' => $cleanTerm ?: $originalQuery,
                'ai_summary' => "Filtrando historial de ventas para '" . ($cleanTerm ?: $originalQuery) . "'."
            ];
        }

        // 8. Servicios
        if (str_contains($lowerQuery, 'servicio') || str_contains($lowerQuery, 'corte') || str_contains($lowerQuery, 'manicura') || str_contains($lowerQuery, 'tinte') || str_contains($lowerQuery, 'peinado')) {
            $cleanTerm = $this->cleanFillerWords($lowerQuery, ['servicios', 'servicio']);
            return [
                'is_safe' => true,
                'target_module' => 'servicios',
                'redirect_url' => route('servicios.index', ['search' => $cleanTerm ?: $originalQuery]),
                'extracted_search' => $cleanTerm ?: $originalQuery,
                'ai_summary' => "Buscando servicio '" . ($cleanTerm ?: $originalQuery) . "'."
            ];
        }

        // 9. Predeterminado: Catálogo de Productos
        $cleanTerm = $this->cleanFillerWords($lowerQuery, ['productos', 'producto', 'catalogo']);
        return [
            'is_safe' => true,
            'target_module' => 'productos',
            'redirect_url' => route('productos.index', ['search' => $cleanTerm ?: $originalQuery]),
            'extracted_search' => $cleanTerm ?: $originalQuery,
            'ai_summary' => "Buscando en el catálogo de productos por '" . ($cleanTerm ?: $originalQuery) . "'."
        ];
    }

    /**
     * Limpia palabras de relleno en español para obtener el término de búsqueda exacto.
     */
    private function cleanFillerWords(string $query, array $keywordsToRemove = []): string
    {
        $fillers = array_merge([
            'buscar', 'buscame', 'muestrame', 'muestra', 'ver', 'dame', 'quiero', 'por favor',
            'de', 'del', 'los', 'las', 'el', 'la', 'un', 'una', 'con', 'en', 'estado', 'de'
        ], $keywordsToRemove);

        $words = explode(' ', $query);
        $filteredWords = array_filter($words, function ($w) use ($fillers) {
            return !in_array(trim($w), $fillers) && strlen(trim($w)) > 1;
        });

        return trim(implode(' ', $filteredWords));
    }
}
