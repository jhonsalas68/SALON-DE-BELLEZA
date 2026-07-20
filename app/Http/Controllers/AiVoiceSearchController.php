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
Tu objetivo es traducir comandos hablados en español estructurados por MÓDULO + BÚSQUEDA a URLs READ-ONLY.

REGLAS DE MÓDULOS Y RUTAS:
1. `proveedor` / `promotor` -> `/promotores?search=TERMINO`
2. `producto` / `inventario` -> `/productos?search=TERMINO` (si dice `stock bajo` -> `/productos?stock_status=bajo`, si dice `vencidos` -> `/productos?vencimiento=proximo`)
3. `servicio` -> `/servicios?search=TERMINO`
4. `cita` / `agenda` -> `/citas?search=TERMINO` (si dice `completada` -> `/citas?estado=completada`, si dice `pendiente` -> `/citas?estado=pendiente`)
5. `venta` / `factura` -> `/ventas?search=TERMINO`
6. `cliente` / `directorio` -> `/clientes?search=TERMINO`
7. `comision` / `estilista` -> `/comisiones?search=TERMINO`
8. `alerta` -> `/alertas`
9. `reporte` / `estadistica` -> `/reportes`
10. `caja` / `arqueo` -> `/cajas`
11. `valoracion` / `estrellas` -> `/valoraciones`
12. `usuario` / `personal` -> `/users?search=TERMINO`

Responde EXCLUSIVAMENTE en JSON puro sin markdown:
{
    "is_safe": true,
    "target_module": "nombre_modulo",
    "redirect_url": "/ruta_con_parametros",
    "extracted_search": "termino",
    "ai_summary": "Explicacion breve en español"
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

        // 3. Motor Inteligente de Respaldo Híbrido Avanzado (Patrón [MÓDULO] + [BÚSQUEDA])
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
     * Motor de interpretación local avanzado estructurado por MÓDULO + BÚSQUEDA.
     */
    private function localIntelligentSearch(string $originalQuery, string $lowerQuery): array
    {
        // 1. Proveedores / Promotores / Suplidores
        if (str_contains($lowerQuery, 'proveedor') || str_contains($lowerQuery, 'promotor') || str_contains($lowerQuery, 'suplidor') || str_contains($lowerQuery, 'distribuidor')) {
            $cleanTerm = $this->cleanFillerWords($lowerQuery, ['proveedores', 'proveedor', 'promotores', 'promotor', 'suplidores', 'suplidor', 'distribuidores', 'distribuidor']);
            return [
                'is_safe' => true,
                'target_module' => 'promotores',
                'redirect_url' => route('promotores.index', $cleanTerm ? ['search' => $cleanTerm] : []),
                'extracted_search' => $cleanTerm ?: 'proveedores',
                'ai_summary' => $cleanTerm ? "Buscando proveedor '{$cleanTerm}'." : "Consultando lista de proveedores."
            ];
        }

        // 2. Productos / Inventario / Catálogo
        if (str_contains($lowerQuery, 'producto') || str_contains($lowerQuery, 'inventario') || str_contains($lowerQuery, 'catalogo')) {
            if (str_contains($lowerQuery, 'stock bajo') || str_contains($lowerQuery, 'poco stock') || str_contains($lowerQuery, 'critico') || str_contains($lowerQuery, 'agotad')) {
                return [
                    'is_safe' => true,
                    'target_module' => 'productos',
                    'redirect_url' => route('productos.index', ['stock_status' => 'bajo']),
                    'extracted_search' => 'stock_bajo',
                    'ai_summary' => "Filtrando productos con stock bajo o crítico."
                ];
            }
            if (str_contains($lowerQuery, 'vencid') || str_contains($lowerQuery, 'proximo')) {
                return [
                    'is_safe' => true,
                    'target_module' => 'productos',
                    'redirect_url' => route('productos.index', ['vencimiento' => 'proximo']),
                    'extracted_search' => 'proximo_vencer',
                    'ai_summary' => "Filtrando productos próximos a vencer."
                ];
            }

            $cleanTerm = $this->cleanFillerWords($lowerQuery, ['productos', 'producto', 'inventario', 'catalogo']);
            return [
                'is_safe' => true,
                'target_module' => 'productos',
                'redirect_url' => route('productos.index', $cleanTerm ? ['search' => $cleanTerm] : []),
                'extracted_search' => $cleanTerm ?: 'productos',
                'ai_summary' => $cleanTerm ? "Buscando producto '{$cleanTerm}'." : "Consultando catálogo de productos."
            ];
        }

        // 3. Servicios / Tratamientos
        if (str_contains($lowerQuery, 'servicio') || str_contains($lowerQuery, 'tratamiento')) {
            $cleanTerm = $this->cleanFillerWords($lowerQuery, ['servicios', 'servicio', 'tratamientos', 'tratamiento']);
            return [
                'is_safe' => true,
                'target_module' => 'servicios',
                'redirect_url' => route('servicios.index', $cleanTerm ? ['search' => $cleanTerm] : []),
                'extracted_search' => $cleanTerm ?: 'servicios',
                'ai_summary' => $cleanTerm ? "Buscando servicio '{$cleanTerm}'." : "Consultando catálogo de servicios."
            ];
        }

        // 4. Citas / Agenda / Reservas
        if (str_contains($lowerQuery, 'cita') || str_contains($lowerQuery, 'agenda') || str_contains($lowerQuery, 'reserva')) {
            $estado = null;
            if (str_contains($lowerQuery, 'completad')) $estado = 'completada';
            elseif (str_contains($lowerQuery, 'pendiente')) $estado = 'pendiente';

            if ($estado) {
                return [
                    'is_safe' => true,
                    'target_module' => 'citas',
                    'redirect_url' => route('citas.index', ['estado' => $estado]),
                    'extracted_search' => $estado,
                    'ai_summary' => "Filtrando citas con estado '{$estado}'."
                ];
            }

            $cleanTerm = $this->cleanFillerWords($lowerQuery, ['citas', 'cita', 'agenda', 'reservas', 'reserva']);
            return [
                'is_safe' => true,
                'target_module' => 'citas',
                'redirect_url' => route('citas.index', $cleanTerm ? ['search' => $cleanTerm] : []),
                'extracted_search' => $cleanTerm ?: 'citas',
                'ai_summary' => $cleanTerm ? "Buscando cita '{$cleanTerm}'." : "Consultando agenda de citas."
            ];
        }

        // 5. Ventas / Facturas / Cobros
        if (str_contains($lowerQuery, 'venta') || str_contains($lowerQuery, 'factura') || str_contains($lowerQuery, 'cobro')) {
            $cleanTerm = $this->cleanFillerWords($lowerQuery, ['ventas', 'venta', 'facturas', 'factura', 'cobros', 'cobro']);
            return [
                'is_safe' => true,
                'target_module' => 'ventas',
                'redirect_url' => route('ventas.index', $cleanTerm ? ['search' => $cleanTerm] : []),
                'extracted_search' => $cleanTerm ?: 'ventas',
                'ai_summary' => $cleanTerm ? "Buscando venta '{$cleanTerm}'." : "Consultando historial de ventas."
            ];
        }

        // 6. Clientes / Directorio
        if (str_contains($lowerQuery, 'cliente') || str_contains($lowerQuery, 'directorio')) {
            $cleanTerm = $this->cleanFillerWords($lowerQuery, ['clientes', 'cliente', 'directorio']);
            return [
                'is_safe' => true,
                'target_module' => 'clientes',
                'redirect_url' => route('clientes.index', $cleanTerm ? ['search' => $cleanTerm] : []),
                'extracted_search' => $cleanTerm ?: 'clientes',
                'ai_summary' => $cleanTerm ? "Buscando cliente '{$cleanTerm}'." : "Consultando directorio de clientes."
            ];
        }

        // 7. Comisiones / Pago a Estilistas
        if (str_contains($lowerQuery, 'comision') || str_contains($lowerQuery, 'pago estilista')) {
            $cleanTerm = $this->cleanFillerWords($lowerQuery, ['comisiones', 'comision', 'pagos', 'pago']);
            return [
                'is_safe' => true,
                'target_module' => 'comisiones',
                'redirect_url' => route('comisiones.index', $cleanTerm ? ['search' => $cleanTerm] : []),
                'extracted_search' => $cleanTerm ?: 'comisiones',
                'ai_summary' => $cleanTerm ? "Filtrando comisiones para '{$cleanTerm}'." : "Consultando panel de comisiones."
            ];
        }

        // 8. Usuarios / Personal / Empleados
        if (str_contains($lowerQuery, 'usuario') || str_contains($lowerQuery, 'personal') || str_contains($lowerQuery, 'empleado')) {
            $cleanTerm = $this->cleanFillerWords($lowerQuery, ['usuarios', 'usuario', 'personal', 'empleados', 'empleado']);
            return [
                'is_safe' => true,
                'target_module' => 'users',
                'redirect_url' => route('users.index', $cleanTerm ? ['search' => $cleanTerm] : []),
                'extracted_search' => $cleanTerm ?: 'usuarios',
                'ai_summary' => $cleanTerm ? "Buscando usuario '{$cleanTerm}'." : "Consultando lista de usuarios del sistema."
            ];
        }

        // 9. Reportes / Estadísticas
        if (str_contains($lowerQuery, 'reporte') || str_contains($lowerQuery, 'estadistica') || str_contains($lowerQuery, 'ingreso')) {
            return [
                'is_safe' => true,
                'target_module' => 'reportes',
                'redirect_url' => route('reportes.index'),
                'extracted_search' => 'reportes',
                'ai_summary' => "Generando reporte ejecutivo del salón."
            ];
        }

        // 10. Arqueo y Caja Chica
        if (str_contains($lowerQuery, 'caja') || str_contains($lowerQuery, 'arqueo') || str_contains($lowerQuery, 'gasto')) {
            return [
                'is_safe' => true,
                'target_module' => 'cajas',
                'redirect_url' => route('cajas.index'),
                'extracted_search' => 'cajas',
                'ai_summary' => "Consultando el módulo de caja chica y arqueos diarios."
            ];
        }

        // 11. Valoraciones y NPS
        if (str_contains($lowerQuery, 'valoracio') || str_contains($lowerQuery, 'califica') || str_contains($lowerQuery, 'nps') || str_contains($lowerQuery, 'estrella')) {
            return [
                'is_safe' => true,
                'target_module' => 'valoraciones',
                'redirect_url' => route('valoraciones.index'),
                'extracted_search' => 'valoraciones',
                'ai_summary' => "Consultando las valoraciones y calificaciones del personal."
            ];
        }

        // 12. Alertas
        if (str_contains($lowerQuery, 'alerta') || str_contains($lowerQuery, 'notifica')) {
            return [
                'is_safe' => true,
                'target_module' => 'alertas',
                'redirect_url' => route('alertas.index'),
                'extracted_search' => 'alertas',
                'ai_summary' => "Consultando centro de alertas."
            ];
        }

        // 13. Búsqueda por Defecto en Catálogo de Productos
        $cleanTerm = $this->cleanFillerWords($lowerQuery);
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
            'de', 'del', 'los', 'las', 'el', 'la', 'un', 'una', 'con', 'en', 'estado', 'de', 'sobre'
        ], $keywordsToRemove);

        $words = explode(' ', $query);
        $filteredWords = array_filter($words, function ($w) use ($fillers) {
            return !in_array(trim($w), $fillers) && strlen(trim($w)) > 1;
        });

        return trim(implode(' ', $filteredWords));
    }
}
