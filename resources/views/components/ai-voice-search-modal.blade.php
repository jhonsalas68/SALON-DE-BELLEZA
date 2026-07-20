<!-- Componente Modal de Buscador Inteligente por Voz con Gemini AI -->
<div id="aiVoiceSearchModal" class="fixed inset-0 z-50 hidden bg-gray-900/60 backdrop-blur-md flex items-center justify-center p-4 transition-opacity duration-300">
    <div class="bg-white rounded-3xl shadow-2xl max-w-lg w-full overflow-hidden border border-rose-100 transform transition-all">
        
        <!-- Cabecera del Modal -->
        <div class="bg-gradient-to-r from-rose-600 to-rose-500 p-6 text-white flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-2xl bg-white/20 flex items-center justify-center backdrop-blur-sm shadow-inner">
                    <i class="fas fa-microphone text-xl text-white animate-bounce"></i>
                </div>
                <div>
                    <h3 class="text-lg font-black tracking-tight flex items-center gap-2">
                        Buscador por Voz
                        <span class="text-[10px] bg-white/30 text-white font-extrabold px-2 py-0.5 rounded-full uppercase">Gemini AI</span>
                    </h3>
                    <p class="text-xs text-rose-100 font-medium">Consultas seguras a la base de datos (READ-ONLY)</p>
                </div>
            </div>
            <button onclick="closeVoiceModal()" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Cuerpo del Modal -->
        <div class="p-6 text-center space-y-6">
            
            <!-- Visualizador de Micrófono / Estado de Escucha -->
            <div class="relative flex flex-col items-center justify-center py-4">
                <!-- Anillos de Onda Sonora cuando está escuchando -->
                <div id="micPulseBg" class="absolute w-28 h-28 bg-rose-500/10 rounded-full animate-ping hidden"></div>
                <div id="micPulseBg2" class="absolute w-20 h-20 bg-rose-500/20 rounded-full animate-pulse hidden"></div>

                <button id="micActiveBtn" onclick="toggleVoiceRecognition()" class="relative z-10 w-20 h-20 bg-gradient-to-tr from-rose-500 to-rose-600 hover:from-rose-600 hover:to-rose-700 text-white rounded-full shadow-lg shadow-rose-300 flex items-center justify-center text-3xl transition-all duration-300 transform active:scale-95 focus:outline-none">
                    <i id="micIcon" class="fas fa-microphone"></i>
                </button>
                
                <p id="voiceStatusText" class="mt-4 text-sm font-extrabold text-gray-700">
                    Haz clic en el micrófono para empezar a hablar
                </p>
                <p class="text-[11px] text-gray-400 font-medium mt-1">
                    Ejemplos: "Buscar productos con poco stock", "Citas de hoy", "Clientes llamadas María"
                </p>
            </div>

            <!-- Transcripción en tiempo real -->
            <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100 text-left min-h-[70px] flex items-center justify-center">
                <p id="transcriptionText" class="text-sm font-semibold text-gray-500 italic text-center">
                    "Escuchando tu voz..."
                </p>
            </div>

            <!-- Entrada manual de respaldo por si no hay micrófono -->
            <div class="flex items-center space-x-2">
                <input type="text" id="manualQueryInput" placeholder="O escribe tu consulta en lenguaje natural..." class="flex-1 px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs font-semibold text-gray-800 focus:outline-none focus:border-rose-500">
                <button onclick="sendManualQuery()" class="px-4 py-2.5 bg-gray-900 hover:bg-black text-white text-xs font-bold rounded-xl shadow transition-all">
                    Enviar
                </button>
            </div>

            <!-- Indicador de carga de Gemini AI -->
            <div id="geminiLoader" class="hidden flex-col items-center space-y-2 py-2">
                <div class="w-8 h-8 border-3 border-rose-500 border-t-transparent rounded-full animate-spin"></div>
                <p class="text-xs font-bold text-rose-600">Gemini AI analizando la consulta...</p>
            </div>

            <!-- Resultado o Aviso de Seguridad de la IA -->
            <div id="aiResultBox" class="hidden p-4 rounded-2xl text-left border"></div>

        </div>

        <!-- Pie del Modal -->
        <div class="p-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between text-xs text-gray-500">
            <span class="flex items-center gap-1 font-semibold">
                <i class="fas fa-shield-alt text-emerald-500"></i>
                Seguridad READ-ONLY activa
            </span>
            <button onclick="closeVoiceModal()" class="font-bold text-gray-600 hover:text-gray-900">
                Cancelar
            </button>
        </div>

    </div>
</div>

<script>
    let recognition = null;
    let isListening = false;

    // Inicializar Web Speech API
    if ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window) {
        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        recognition = new SpeechRecognition();
        recognition.lang = 'es-ES';
        recognition.continuous = false;
        recognition.interimResults = true;

        recognition.onstart = function() {
            isListening = true;
            updateVoiceUI(true, 'Escuchando tu voz... habla ahora', 'fas fa-microphone-alt text-rose-200 animate-pulse');
            document.getElementById('transcriptionText').textContent = 'Escuchando...';
            document.getElementById('transcriptionText').classList.add('text-rose-600', 'font-bold');
        };

        recognition.onresult = function(event) {
            let interimTranscript = '';
            let finalTranscript = '';

            for (let i = event.resultIndex; i < event.results.length; ++i) {
                if (event.results[i].isFinal) {
                    finalTranscript += event.results[i][0].transcript;
                } else {
                    interimTranscript += event.results[i][0].transcript;
                }
            }

            const currentText = finalTranscript || interimTranscript;
            if (currentText) {
                document.getElementById('transcriptionText').textContent = '"' + currentText + '"';
            }

            if (finalTranscript) {
                recognition.stop();
                processVoiceQueryWithGemini(finalTranscript);
            }
        };

        recognition.onerror = function(event) {
            console.error('Speech recognition error:', event.error);
            updateVoiceUI(false, 'No se pudo detectar voz. Intenta de nuevo o escribe abajo.');
            document.getElementById('transcriptionText').textContent = 'Error de micrófono: ' + event.error;
        };

        recognition.onend = function() {
            isListening = false;
            updateVoiceUI(false, 'Haz clic en el micrófono para hablar');
        };
    } else {
        console.warn('SpeechRecognition API no soportada en este navegador.');
    }

    function openVoiceModal() {
        document.getElementById('aiVoiceSearchModal').classList.remove('hidden');
        document.getElementById('aiResultBox').classList.add('hidden');
        document.getElementById('geminiLoader').classList.add('hidden');
        document.getElementById('transcriptionText').textContent = '"Haz clic en el micrófono para empezar"';
        document.getElementById('transcriptionText').className = 'text-sm font-semibold text-gray-500 italic text-center';
        document.getElementById('manualQueryInput').value = '';
    }

    function closeVoiceModal() {
        if (recognition && isListening) {
            recognition.stop();
        }
        document.getElementById('aiVoiceSearchModal').classList.add('hidden');
    }

    function toggleVoiceRecognition() {
        if (!recognition) {
            alert('Tu navegador no soporta el reconocimiento directo por voz. Puedes escribir tu consulta en el campo de texto manual.');
            return;
        }

        if (isListening) {
            recognition.stop();
        } else {
            try {
                recognition.start();
            } catch(e) {
                console.error(e);
            }
        }
    }

    function updateVoiceUI(active, text, iconClass = 'fas fa-microphone') {
        const pulse1 = document.getElementById('micPulseBg');
        const pulse2 = document.getElementById('micPulseBg2');
        const statusText = document.getElementById('voiceStatusText');
        const micIcon = document.getElementById('micIcon');

        if (active) {
            pulse1.classList.remove('hidden');
            pulse2.classList.remove('hidden');
            statusText.textContent = text;
            statusText.className = 'mt-4 text-sm font-extrabold text-rose-600 animate-pulse';
            micIcon.className = iconClass;
        } else {
            pulse1.classList.add('hidden');
            pulse2.classList.add('hidden');
            statusText.textContent = text;
            statusText.className = 'mt-4 text-sm font-extrabold text-gray-700';
            micIcon.className = 'fas fa-microphone';
        }
    }

    function sendManualQuery() {
        const query = document.getElementById('manualQueryInput').value.trim();
        if (query) {
            document.getElementById('transcriptionText').textContent = '"' + query + '"';
            processVoiceQueryWithGemini(query);
        }
    }

    function processVoiceQueryWithGemini(queryText) {
        document.getElementById('geminiLoader').classList.remove('hidden');
        document.getElementById('geminiLoader').classList.add('flex');
        document.getElementById('aiResultBox').classList.add('hidden');

        fetch('{{ route("ai.voice-search") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ query: queryText })
        })
        .then(response => response.json())
        .then(res => {
            document.getElementById('geminiLoader').classList.add('hidden');
            document.getElementById('geminiLoader').classList.remove('flex');
            
            const resultBox = document.getElementById('aiResultBox');
            resultBox.classList.remove('hidden');

            if (res.success && res.data) {
                const data = res.data;
                if (data.is_safe) {
                    resultBox.className = 'p-4 rounded-2xl text-left border bg-emerald-50 border-emerald-200 text-emerald-900';
                    resultBox.innerHTML = `
                        <div class="flex items-start space-x-3">
                            <i class="fas fa-check-circle text-emerald-600 text-xl mt-0.5"></i>
                            <div class="flex-1">
                                <h4 class="font-extrabold text-xs uppercase tracking-wider text-emerald-700 mb-1">Gemini AI - Interpretación Exitosa</h4>
                                <p class="text-sm font-bold text-emerald-900 mb-3">${data.ai_summary}</p>
                                <a href="${data.redirect_url}" class="inline-flex items-center space-x-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow transition-all">
                                    <span>Ver Resultados Filtrados</span>
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    `;

                    // Redirección automática tras 1.5 segundos
                    setTimeout(() => {
                        window.location.href = data.redirect_url;
                    }, 1500);

                } else {
                    resultBox.className = 'p-4 rounded-2xl text-left border bg-rose-50 border-rose-200 text-rose-900';
                    resultBox.innerHTML = `
                        <div class="flex items-start space-x-3">
                            <i class="fas fa-shield-alt text-rose-600 text-xl mt-0.5"></i>
                            <div>
                                <h4 class="font-extrabold text-xs uppercase tracking-wider text-rose-700 mb-1">Acción Restringida por Seguridad</h4>
                                <p class="text-sm font-semibold text-rose-900">${data.ai_summary}</p>
                            </div>
                        </div>
                    `;
                }
            } else {
                resultBox.className = 'p-4 rounded-2xl text-left border bg-amber-50 border-amber-200 text-amber-900';
                resultBox.innerHTML = `
                    <div class="flex items-start space-x-3">
                        <i class="fas fa-exclamation-triangle text-amber-600 text-xl mt-0.5"></i>
                        <div>
                            <h4 class="font-extrabold text-xs uppercase tracking-wider text-amber-700 mb-1">Aviso del Asistente</h4>
                            <p class="text-sm font-semibold text-amber-900">${res.message || 'No se pudo interpretar el comando. Intenta expresar tu consulta de otra forma.'}</p>
                        </div>
                    </div>
                `;
            }
        })
        .catch(err => {
            console.error(err);
            document.getElementById('geminiLoader').classList.add('hidden');
            document.getElementById('geminiLoader').classList.remove('flex');
            
            const resultBox = document.getElementById('aiResultBox');
            resultBox.classList.remove('hidden');
            resultBox.className = 'p-4 rounded-2xl text-left border bg-rose-50 border-rose-200 text-rose-900';
            resultBox.innerHTML = `
                <div class="flex items-start space-x-3">
                    <i class="fas fa-times-circle text-rose-600 text-xl mt-0.5"></i>
                    <div>
                        <h4 class="font-extrabold text-xs uppercase tracking-wider text-rose-700 mb-1">Error de Conexión</h4>
                        <p class="text-sm font-semibold text-rose-900">Hubo un problema al procesar la voz con la API de Gemini.</p>
                    </div>
                </div>
            `;
        });
    }
</script>
