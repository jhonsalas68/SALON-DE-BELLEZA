@extends('layouts.app')

@section('title', 'Dashboard - Salón de Belleza Anita')

@section('header')
<div class="flex justify-between items-center mb-8">
    <div>
        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">¡Bienvenida de nuevo!</h1>
        <p class="text-gray-500 font-medium">Aquí está el resumen de hoy en Anita Salon.</p>
    </div>
    <div class="flex items-center space-x-2 bg-indigo-50 px-4 py-2 rounded-2xl border border-indigo-100">
        <i class="far fa-calendar-alt text-indigo-500"></i>
        <span class="text-indigo-700 font-bold text-sm">{{ now()->format('d M, Y') }}</span>
    </div>
</div>
@endsection

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
    <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 hover:shadow-xl hover:scale-[1.02] transition-all duration-300">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-blue-100 rounded-2xl flex items-center justify-center">
                <i class="fas fa-calendar-check text-blue-600 text-xl"></i>
            </div>
            <span class="text-[10px] font-bold text-blue-500 uppercase bg-blue-50 px-2 py-1 rounded-full">Hoy</span>
        </div>
        <h3 class="text-3xl font-black text-gray-800">12</h3>
        <p class="text-gray-500 text-sm font-medium">Citas Agendadas</p>
    </div>

    <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 hover:shadow-xl hover:scale-[1.02] transition-all duration-300">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-green-100 rounded-2xl flex items-center justify-center">
                <i class="fas fa-dollar-sign text-green-600 text-xl"></i>
            </div>
            <span class="text-[10px] font-bold text-green-500 uppercase bg-green-50 px-2 py-1 rounded-full">Ventas</span>
        </div>
        <h3 class="text-3xl font-black text-gray-800">$2,450</h3>
        <p class="text-gray-500 text-sm font-medium">Ingresos Totales</p>
    </div>

    <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 hover:shadow-xl hover:scale-[1.02] transition-all duration-300">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-purple-100 rounded-2xl flex items-center justify-center">
                <i class="fas fa-users text-purple-600 text-xl"></i>
            </div>
            <span class="text-[10px] font-bold text-purple-500 uppercase bg-purple-50 px-2 py-1 rounded-full">Clientes</span>
        </div>
        <h3 class="text-3xl font-black text-gray-800">156</h3>
        <p class="text-gray-500 text-sm font-medium">Activos este mes</p>
    </div>

    <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 hover:shadow-xl hover:scale-[1.02] transition-all duration-300">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-pink-100 rounded-2xl flex items-center justify-center">
                <i class="fas fa-star text-pink-600 text-xl"></i>
            </div>
            <span class="text-[10px] font-bold text-pink-500 uppercase bg-pink-50 px-2 py-1 rounded-full">Rating</span>
        </div>
        <h3 class="text-3xl font-black text-gray-800">4.8</h3>
        <p class="text-gray-500 text-sm font-medium">Calificación Media</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <div class="bg-gray-50 p-8 rounded-[2.5rem] border border-gray-100">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-black text-gray-800">
                <i class="fas fa-clock mr-2 text-indigo-500"></i>
                Próximas Citas
            </h2>
            <a href="#" class="text-indigo-600 text-sm font-bold hover:underline">Ver todas</a>
        </div>
        <div class="space-y-4">
            <div class="flex items-center justify-between p-4 bg-white rounded-2xl shadow-sm border border-gray-50 transition hover:shadow-md">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-indigo-50 rounded-xl flex items-center justify-center">
                        <i class="fas fa-user text-indigo-400"></i>
                    </div>
                    <div>
                        <p class="font-bold text-gray-800">María García</p>
                        <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest">Corte y Color</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="font-black text-gray-800">10:00 AM</p>
                    <p class="text-[10px] font-bold text-green-500 uppercase bg-green-50 px-2 py-1 rounded-lg">Confirmado</p>
                </div>
            </div>
            <div class="flex items-center justify-between p-4 bg-white rounded-2xl shadow-sm border border-gray-50 transition hover:shadow-md">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-pink-50 rounded-xl flex items-center justify-center">
                        <i class="fas fa-user text-pink-400"></i>
                    </div>
                    <div>
                        <p class="font-bold text-gray-800">Ana Martínez</p>
                        <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest">Manicure Spa</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="font-black text-gray-800">02:30 PM</p>
                    <p class="text-[10px] font-bold text-yellow-500 uppercase bg-yellow-50 px-2 py-1 rounded-lg">Pendiente</p>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-gray-50 p-8 rounded-[2.5rem] border border-gray-100">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-black text-gray-800">
                <i class="fas fa-chart-line mr-2 text-pink-500"></i>
                Tendencia de Servicios
            </h2>
        </div>
        <div class="space-y-4">
            <div class="p-4 bg-white rounded-2xl border border-gray-100">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm font-bold text-gray-700">Corte de Cabello</span>
                    <span class="text-xs font-bold text-gray-400">75%</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-2">
                    <div class="bg-indigo-500 h-2 rounded-full" style="width: 75%"></div>
                </div>
            </div>
            <div class="p-4 bg-white rounded-2xl border border-gray-100">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm font-bold text-gray-700">Coloración</span>
                    <span class="text-xs font-bold text-gray-400">45%</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-2">
                    <div class="bg-pink-500 h-2 rounded-full" style="width: 45%"></div>
                </div>
            </div>
            <div class="p-4 bg-white rounded-2xl border border-gray-100">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm font-bold text-gray-700">Manicure</span>
                    <span class="text-xs font-bold text-gray-400">60%</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-2">
                    <div class="bg-purple-500 h-2 rounded-full" style="width: 60%"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
