<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anita Salon | Experiencia de Belleza</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Plus+Jakarta+Sans:wght@200..800&display=swap');
        
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #faf8f6; scroll-behavior: smooth; }
        .font-serif { font-family: 'Playfair Display', serif; }

        .image-collage-card {
            border-radius: 100px;
            overflow: hidden;
            transition: transform 0.5s ease;
        }

        .image-collage-card:hover {
            transform: scale(1.05);
        }

        .service-card {
            background: #fff;
            border-radius: 2rem;
            transition: all 0.4s ease;
        }

        .service-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.05);
        }

        .login-section {
            background: linear-gradient(135deg, #fdfcfb 0%, #e2d1c3 100%);
        }

        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #faf8f6; }
        ::-webkit-scrollbar-thumb { background: #fb7185; border-radius: 10px; }
    </style>
</head>
<body class="text-gray-800">

    <!-- Header -->
    <header class="fixed top-0 w-full z-50 bg-white/80 backdrop-blur-md border-b border-rose-50 px-8 py-5 flex justify-between items-center">
        <div class="flex items-center space-x-3">
            <div class="w-8 h-8 bg-rose-500 rounded-lg flex items-center justify-center">
                <i class="fas fa-spa text-white text-sm"></i>
            </div>
            <span class="text-xl font-bold tracking-tighter">Anita<span class="text-rose-500">Salon</span></span>
        </div>
        <nav class="hidden lg:flex space-x-10 text-[11px] font-bold uppercase tracking-widest text-gray-400">
            <a href="#inicio" class="hover:text-rose-500 transition">Inicio</a>
            <a href="#nosotros" class="hover:text-rose-500 transition">Nosotros</a>
            <a href="#servicios" class="hover:text-rose-500 transition">Tratamientos</a>
            <a href="#contacto" class="hover:text-rose-500 transition">Contacto</a>
        </nav>
        <a href="#acceso" class="bg-rose-500 hover:bg-rose-600 text-white px-6 py-2.5 rounded-full text-[10px] font-bold uppercase tracking-widest transition shadow-lg shadow-rose-100">
            Reservaciones
        </a>
    </header>

    <!-- Hero Section -->
    <section id="inicio" class="min-h-screen pt-32 pb-20 px-6 max-w-7xl mx-auto flex flex-col items-center text-center">
        <h1 class="text-5xl lg:text-8xl font-serif leading-tight mb-8">
            En un cuerpo relajado,<br>
            un <span class="italic text-rose-500">alma en paz.</span>
        </h1>
        <p class="text-gray-400 max-w-2xl mb-12 leading-relaxed">
            Tu refugio de bienestar en el corazón de la ciudad. Redescubre tu belleza natural con nuestros tratamientos de última generación.
        </p>
        
        <!-- Collage Visually -->
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 w-full h-[300px] lg:h-[450px]">
            <div class="image-collage-card h-full">
                <img src="https://images.unsplash.com/photo-1560750588-73207b1ef5b8?auto=format&fit=crop&q=80&w=800" class="w-full h-full object-cover" alt="Salon Interior">
            </div>
            <div class="image-collage-card h-full lg:translate-y-12">
                <img src="https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?auto=format&fit=crop&q=80&w=800" class="w-full h-full object-cover" alt="Hairdressing">
            </div>
            <div class="image-collage-card h-full rounded-[2rem] lg:col-span-1">
                <img src="https://images.unsplash.com/photo-1521590832167-7bcbfaa6381f?auto=format&fit=crop&q=80&w=800" class="w-full h-full object-cover" alt="Relaxing">
            </div>
            <div class="image-collage-card h-full lg:translate-y-12">
                <img src="https://images.unsplash.com/photo-1562322140-8baeececf3df?auto=format&fit=crop&q=80&w=800" class="w-full h-full object-cover" alt="Aesthetics">
            </div>
            <div class="image-collage-card h-full">
                <img src="https://images.unsplash.com/photo-1487412912498-0447578fcca8?auto=format&fit=crop&q=80&w=800" class="w-full h-full object-cover" alt="Beauty">
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="nosotros" class="py-32 px-6 bg-white">
        <div class="max-w-7xl mx-auto grid lg:grid-cols-2 gap-20 items-center">
            <div class="relative">
                <img src="https://images.unsplash.com/photo-1552693673-1bf958298935?auto=format&fit=crop&q=80&w=1200" class="rounded-[3rem] w-full shadow-2xl" alt="About Anita Salon">
                <div class="absolute -bottom-10 -right-10 bg-rose-500 p-10 rounded-[2rem] text-white hidden lg:block">
                    <p class="text-4xl font-serif italic mb-1">10+</p>
                    <p class="text-[10px] font-bold uppercase tracking-widest">Años de Experticia</p>
                </div>
            </div>
            <div>
                <span class="text-rose-500 font-bold uppercase tracking-[0.3em] text-[10px] mb-4 block">Nuestra Historia</span>
                <h2 class="text-5xl font-serif italic mb-8 leading-tight">Dedicadas a resaltar <br> tu esencia única.</h2>
                <div class="space-y-6 text-gray-500 leading-relaxed">
                    <p>En Anita Salon, creemos que la belleza no es solo algo exterior, sino un reflejo del equilibrio interior. Nuestro equipo de profesionales certificados te guiará en un viaje de transformación.</p>
                    <p>Utilizamos exclusivamente productos orgánicos y tecnología de punta para asegurar resultados excepcionales que respeten tu bienestar.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Grid -->
    <section id="servicios" class="py-32 px-6">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-20">
                <h2 class="text-5xl font-serif italic mb-4">Tratamientos Exclusivos</h2>
                <p class="text-gray-400">Descubre nuestros servicios diseñados para cada necesidad.</p>
            </div>
            <div class="grid md:grid-cols-3 gap-8">
                <!-- S1: Facial -->
                <div class="service-card overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1570172619644-dfd03ed5d881?auto=format&fit=crop&q=80&w=800" class="w-full h-80 object-cover" alt="Facial">
                    <div class="p-8">
                        <h3 class="text-xl font-bold mb-3">Facial Profundo</h3>
                        <p class="text-gray-400 text-sm leading-relaxed">Tratamiento rejuvenecedor con extractos naturales para una piel radiante.</p>
                    </div>
                </div>
                <!-- S2: Maquillaje -->
                <div class="service-card overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1487412947147-5cebf100ffc2?auto=format&fit=crop&q=80&w=800" class="w-full h-80 object-cover" alt="Maquillaje Profesional">
                    <div class="p-8">
                        <h3 class="text-xl font-bold mb-3">Maquillaje Profesional</h3>
                        <p class="text-gray-400 text-sm leading-relaxed">Resaltamos tu belleza para eventos especiales con productos de alta gama.</p>
                    </div>
                </div>
                <!-- S3: Cuidado Capilar -->
                <div class="service-card overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1560869713-7d0a29430803?auto=format&fit=crop&q=80&w=800" class="w-full h-80 object-cover" alt="Cuidado Capilar">
                    <div class="p-8">
                        <h3 class="text-xl font-bold mb-3">Cuidado Capilar</h3>
                        <p class="text-gray-400 text-sm leading-relaxed">Hidratación y brillo para un cabello saludable y con vida.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Login Area (Access) -->
    <section id="acceso" class="py-32 px-6 login-section">
        <div class="max-w-7xl mx-auto grid lg:grid-cols-2 gap-20 items-center">
            <div>
                <h2 class="text-6xl font-serif leading-tight mb-8">Comienza <br>tu gestión hoy.</h2>
                <p class="text-gray-500 max-w-sm">Si eres parte de nuestro staff, accede aquí para gestionar las citas y servicios del salón.</p>
            </div>
            <div class="bg-white p-12 lg:p-16 rounded-[3.5rem] shadow-2xl relative overflow-hidden" id="authCard">
                <div class="mb-10 text-center relative z-10">
                    <div class="w-16 h-16 bg-rose-500 rounded-2xl flex items-center justify-center mb-6 shadow-xl shadow-rose-100 mx-auto">
                        <i class="fas fa-heart text-white text-2xl"></i>
                    </div>
                    <h3 class="text-3xl font-serif italic mb-2" id="authTitle">Únete a nuestra esencia.</h3>
                    <p class="text-gray-400 text-sm" id="authSubtitle">Gestiona la magia detrás del salón.</p>
                </div>

                @if ($errors->any())
                    <div class="bg-rose-50 text-rose-600 text-[10px] p-4 rounded-xl mb-8 font-bold text-center border border-rose-100">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form id="authForm" action="{{ route('login.post') }}" method="POST" class="space-y-5 relative z-10">
                    @csrf
                    <!-- Name Field (Register only) -->
                    <div id="nameGroup" class="hidden">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest pl-1 mb-2 block">Nombre Completo</label>
                        <input type="text" name="name" 
                               class="w-full px-6 py-4 rounded-3xl bg-gray-50 border border-gray-100 focus:border-rose-300 outline-none transition text-sm">
                    </div>

                    <div>
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest pl-1 mb-2 block">Email Profesional</label>
                        <input type="email" name="email" required 
                               class="w-full px-6 py-4 rounded-3xl bg-gray-50 border border-gray-100 focus:border-rose-300 outline-none transition text-sm">
                    </div>

                    <div>
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest pl-1 mb-2 block">Contraseña</label>
                        <input type="password" name="password" required 
                               class="w-full px-6 py-4 rounded-3xl bg-gray-50 border border-gray-100 focus:border-rose-300 outline-none transition text-sm">
                    </div>

                    <!-- Confirm Password (Register only) -->
                    <div id="confirmGroup" class="hidden">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest pl-1 mb-2 block">Confirmar Contraseña</label>
                        <input type="password" name="password_confirmation" 
                               class="w-full px-6 py-4 rounded-3xl bg-gray-50 border border-gray-100 focus:border-rose-300 outline-none transition text-sm">
                    </div>

                    <button type="submit" id="submitBtn" class="w-full bg-rose-500 hover:bg-rose-600 text-white font-bold py-5 rounded-3xl transition-all shadow-xl shadow-rose-100 text-sm mt-4">
                        Entrar al Sistema
                    </button>
                </form>

                <div class="mt-8 text-center relative z-10">
                    <p class="text-xs text-gray-400" id="toggleText">
                        ¿Aún no tienes cuenta? 
                        <button onclick="toggleAuth()" class="text-rose-500 font-bold hover:underline ml-1">Regístrate aquí</button>
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-white py-20 px-6 border-t border-rose-50">
        <div class="max-w-7xl mx-auto flex flex-col items-center">
            <div class="text-2xl font-bold tracking-tighter mb-8">Anita<span class="text-rose-500">Salon</span></div>
            <div class="flex space-x-10 text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-10">
                <a href="#" class="hover:text-rose-500">Instagram</a>
                <a href="#" class="hover:text-rose-500">Facebook</a>
                <a href="#" class="hover:text-rose-500">LinkedIn</a>
            </div>
            <p class="text-gray-300 text-[10px] uppercase font-bold tracking-[0.2em]">Anita Salon & Wellness © 2026. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script>
        let isRegister = false;
        const form = document.getElementById('authForm');
        const nameGroup = document.getElementById('nameGroup');
        const confirmGroup = document.getElementById('confirmGroup');
        const submitBtn = document.getElementById('submitBtn');
        const authTitle = document.getElementById('authTitle');
        const authSubtitle = document.getElementById('authSubtitle');
        const toggleText = document.getElementById('toggleText');

        function toggleAuth() {
            isRegister = !isRegister;
            
            if(isRegister) {
                form.action = "{{ route('register.post') }}";
                nameGroup.classList.remove('hidden');
                confirmGroup.classList.remove('hidden');
                submitBtn.innerText = 'Crear Mi Cuenta';
                authTitle.innerText = 'Empieza tu viaje.';
                authSubtitle.innerText = 'Crea tu perfil para unirte al equipo.';
                toggleText.innerHTML = '¿Ya tienes cuenta? <button onclick="toggleAuth()" class="text-rose-500 font-bold hover:underline ml-1">Inicia sesión</button>';
            } else {
                form.action = "{{ route('login.post') }}";
                nameGroup.classList.add('hidden');
                confirmGroup.classList.add('hidden');
                submitBtn.innerText = 'Entrar al Sistema';
                authTitle.innerText = 'Únete a nuestra esencia.';
                authSubtitle.innerText = 'Gestiona la magia detrás del salón.';
                toggleText.innerHTML = '¿Aún no tienes cuenta? <button onclick="toggleAuth()" class="text-rose-500 font-bold hover:underline ml-1">Regístrate aquí</button>';
            }
        }
    </script>

</body>
</html>
