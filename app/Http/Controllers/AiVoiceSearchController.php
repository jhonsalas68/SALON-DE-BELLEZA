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
        $apiKey = config('services.gemini.api_key');

        if (empty($apiKey)) {
            return response()->json([
                'success' => false,
                'message' => 'La API Key de Gemini no está configurada.'
            ], 500);
        }

        $systemPrompt = <<<PROMPT
Eres el Asistente IA de Búsqueda del sistema del Salón de Belleza ("Salón Anita").
Tu función es interpretar comandos de voz en lenguaje natural en español y traducirlos ÚNICAMENTE a filtros y consultas de LECTURA (READ-ONLY) sobre la base de datos.

MÓDULOS DISPONIBLES EN EL SISTEMA:
1. `productos`: Catálogo e inventario de productos. Ejemplo ruta: `/productos?search=shampoo` o `/productos?stock=bajo`
2. `servicios`: Servicios de salón (cortes, tinte, uñas). Ejemplo ruta: `/servicios?search=corte`
3. `citas`: Agenda de citas de clientes y estilistas. Ejemplo ruta: `/citas?search=Maria` o `/citas?estado=completada` o `/citas?estado=pendiente`
4. `ventas`: Historial de ventas registradas. Ejemplo ruta: `/ventas?search=Maria`
5. `clientes`: Directorio de clientes. Ejemplo ruta: `/clientes?search=Juan`
6. `comisiones`: Comisiones de estilistas. Ejemplo ruta: `/comisiones?estado=pendiente`
7. `alertas`: Alertas de inventario con stock mínimo. Ejemplo ruta: `/alertas`
8. `reportes`: Reportes administrativos. Ejemplo ruta: `/reportes?rango=hoy` o `/reportes?rango=mes` o `/reportes?rango=semana`
9. `promociones`: Promociones activas. Ejemplo ruta: `/promociones?search=descuento`

REGLAS DE SEGURIDAD CRÍTICAS:
- El sistema SOLO PERMITE CONSULTAS Y FILTROS DE LECTURA.
- Si el usuario dice cosas que impliquen MODIFICAR, ELIMINAR, CREAR, BORRAR, CANCELAR O ALTERAR DATOS (ej: "eliminar usuario", "borrar citas", "cambiar precio", "drop table"), DEBES MARCAR `is_safe`: false y explicar en `ai_summary` que por seguridad solo se permiten búsquedas y consultas de lectura.

DEBES RESPONDER EXCLUSIVAMENTE EN FORMATO JSON VÁLIDO SIN BLOQUES DE CÓDIGO MARKDOWN O TEXTO EXTRA:
{
    "is_safe": true,
    "target_module": "nombre_del_modulo",
    "redirect_url": "/ruta_con_parametros",
    "extracted_search": "termino_clave",
    "ai_summary": "Explicación breve y amigable en español de lo que se va a mostrar o filtrar"
}
PROMPT;

        try {
            // Llamada a la API REST de Google Gemini
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$apiKey}", [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $systemPrompt],
                            ['text' => "Comando de voz recibido del usuario: \"{$userQuery}\""]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.1,
                    'responseMimeType' => 'application/json'
                ]
            ]);

            if ($response->failed()) {
                Log::error('Gemini API Error: ' . $response->body());
                return response()->json([
                    'success' => false,
                    'message' => 'Error al comunicarse con Gemini AI.'
                ], 500);
            }

            $responseData = $response->json();
            $rawContent = $responseData['candidates'][0]['content']['parts'][0]['text'] ?? '{}';
            
            // Clean content if markdown block
            $cleanJson = preg_replace('/^```json\s*|\s*```$/i', '', trim($rawContent));
            $aiResult = json_decode($cleanJson, true);

            if (!$aiResult || !isset($aiResult['is_safe'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se pudo interpretar la consulta de voz.'
                ], 422);
            }

            if (auth()->check()) {
                $this->logActivity('AI_VOICE_SEARCH', "Búsqueda por voz Gemini: '{$userQuery}'", [
                    'query' => $userQuery,
                    'ai_result' => $aiResult
                ]);
            }

            return response()->json([
                'success' => true,
                'user_query' => $userQuery,
                'data' => $aiResult
            ]);

        } catch (\Exception $e) {
            Log::error('AiVoiceSearchController Exception: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Excepción en el servicio de IA por Voz: ' . $e->getMessage()
            ], 500);
        }
    }
}
