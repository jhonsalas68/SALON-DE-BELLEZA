<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salón de Belleza Anita - Registrarse</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.4);
        }
        .input-group {
            position: relative;
        }
        .input-group input:focus + label,
        .input-group input:not(:placeholder-shown) + label {
            transform: translateY(-32px) scale(0.85);
            color: #667eea;
            background: white;
            padding: 0 8px;
        }
        .floating-label {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            transition: all 0.3s ease;
            pointer-events: none;
            color: #9ca3af;
            background: transparent;
            padding: 0 4px;
        }
    </style>
</head>
<body class="flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="glass-effect rounded-2xl shadow-2xl p-8">
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-purple-500 to-pink-500 rounded-full mb-4">
                    <i class="fas fa-spa text-white text-3xl"></i>
                </div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Salón de Belleza Anita</h1>
                <p class="text-gray-600">Crea tu cuenta</p>
            </div>

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('register.post') }}" method="POST" class="space-y-6">
                @csrf
                <div class="input-group">
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        placeholder=" "
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition"
                        value="{{ old('name') }}"
                        required
                    >
                    <label for="name" class="floating-label">Nombre Completo</label>
                    <i class="fas fa-user absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>

                <div class="input-group">
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        placeholder=" "
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition"
                        value="{{ old('email') }}"
                        required
                    >
                    <label for="email" class="floating-label">Correo Electrónico</label>
                    <i class="fas fa-envelope absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>

                <div class="input-group">
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        placeholder=" "
                        class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition"
                        required
                    >
                    <label for="password" class="floating-label">Contraseña</label>
                    <i class="fas fa-lock absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>

                <div class="input-group">
                    <input 
                        type="password" 
                        id="password_confirmation" 
                        name="password_confirmation" 
                        placeholder=" "
                        class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition"
                        required
                    >
                    <label for="password_confirmation" class="floating-label">Confirmar Contraseña</label>
                    <i class="fas fa-lock absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>

                <button type="submit" class="btn-primary w-full py-3 rounded-lg text-white font-semibold">
                    <i class="fas fa-user-plus mr-2"></i>
                    Crear Cuenta
                </button>
            </form>

            <div class="mt-8 text-center">
                <p class="text-gray-600">
                    ¿Ya tienes cuenta? 
                    <a href="{{ route('login') }}" class="text-purple-600 hover:text-purple-700 font-semibold transition">
                        Inicia sesión aquí
                    </a>
                </p>
            </div>

            <div class="mt-6 pt-6 border-t border-gray-200">
                <div class="flex items-center justify-center space-x-4 text-sm text-gray-500">
                    <span><i class="fas fa-shield-alt mr-1"></i>Seguro</span>
                    <span><i class="fas fa-lock mr-1"></i>Privado</span>
                    <span><i class="fas fa-heart mr-1"></i>Profesional</span>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
