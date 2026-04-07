<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Salón de Belleza Anita</title>
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
        .card {
            transition: all 0.3s ease;
            user-select: none;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
        .card h3, .card p, .card span {
            user-select: none;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
        }
    </style>
</head>
<body class="p-4">
    <div class="max-w-7xl mx-auto">
        <header class="glass-effect rounded-2xl shadow-xl p-6 mb-8">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-500 rounded-full flex items-center justify-center">
                        <i class="fas fa-spa text-white text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Salón de Belleza Anita</h1>
                        <p class="text-gray-600">Panel de Administración</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="text-right">
                        <p class="text-sm text-gray-600">Bienvenida,</p>
                        <p class="font-semibold text-gray-800">{{ $user->email }}</p>
                    </div>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition flex items-center space-x-2">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Salir</span>
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="card glass-effect rounded-xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-calendar-check text-blue-600 text-xl"></i>
                    </div>
                    <span class="text-sm text-gray-500">Hoy</span>
                </div>
                <h3 class="text-2xl font-bold text-gray-800">12</h3>
                <p class="text-gray-600">Citas Agendadas</p>
            </div>

            <div class="card glass-effect rounded-xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-dollar-sign text-green-600 text-xl"></i>
                    </div>
                    <span class="text-sm text-gray-500">Este mes</span>
                </div>
                <h3 class="text-2xl font-bold text-gray-800">$2,450</h3>
                <p class="text-gray-600">Ingresos Totales</p>
            </div>

            <div class="card glass-effect rounded-xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-users text-purple-600 text-xl"></i>
                    </div>
                    <span class="text-sm text-gray-500">Total</span>
                </div>
                <h3 class="text-2xl font-bold text-gray-800">156</h3>
                <p class="text-gray-600">Clientes Activos</p>
            </div>

            <div class="card glass-effect rounded-xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-pink-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-star text-pink-600 text-xl"></i>
                    </div>
                    <span class="text-sm text-gray-500">Promedio</span>
                </div>
                <h3 class="text-2xl font-bold text-gray-800">4.8</h3>
                <p class="text-gray-600">Calificación</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="glass-effect rounded-xl p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">
                    <i class="fas fa-clock mr-2 text-purple-600"></i>
                    Próximas Citas
                </h2>
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 bg-white rounded-lg border border-gray-200">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-user text-purple-600"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800">María García</p>
                                <p class="text-sm text-gray-600">Corte y Color</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-gray-800">10:00 AM</p>
                            <p class="text-sm text-gray-600">Hoy</p>
                        </div>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-white rounded-lg border border-gray-200">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-pink-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-user text-pink-600"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800">Ana Martínez</p>
                                <p class="text-sm text-gray-600">Manicure</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-gray-800">2:30 PM</p>
                            <p class="text-sm text-gray-600">Hoy</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="glass-effect rounded-xl p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">
                    <i class="fas fa-chart-line mr-2 text-purple-600"></i>
                    Servicios Populares
                </h2>
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 bg-white rounded-lg border border-gray-200">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-cut text-blue-600"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800">Corte de Cabello</p>
                                <p class="text-sm text-gray-600">45 servicios este mes</p>
                            </div>
                        </div>
                        <span class="text-lg font-bold text-blue-600">$25</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-white rounded-lg border border-gray-200">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-pink-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-paint-brush text-pink-600"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800">Coloración</p>
                                <p class="text-sm text-gray-600">32 servicios este mes</p>
                            </div>
                        </div>
                        <span class="text-lg font-bold text-pink-600">$60</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
