@extends('layouts.app')

@section('title', 'Promotores - Salon Anita')

@section('header')
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-3xl font-extrabold text-gray-800 tracking-tight">Promotores</h2>
            <p class="text-gray-500 font-medium">Gestiona los proveedores y promotores de marca.</p>
        </div>
        <a href="{{ route('promotores.create') }}" class="bg-rose-500 hover:bg-rose-600 text-white px-6 py-3 rounded-2xl font-bold transition-all shadow-lg shadow-rose-100 flex items-center justify-center space-x-2">
            <i class="fas fa-plus text-xs"></i>
            <span>Nuevo Promotor</span>
        </a>
    </div>
@endsection

@section('content')
<div class="bg-white rounded-3xl shadow-sm border border-rose-50 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-rose-50/30">
                    <th class="p-5 text-xs font-black text-rose-400 uppercase tracking-widest border-b border-rose-50">Promotor</th>
                    <th class="p-5 text-xs font-black text-rose-400 uppercase tracking-widest border-b border-rose-50">Empresa</th>
                    <th class="p-5 text-xs font-black text-rose-400 uppercase tracking-widest border-b border-rose-50">Contacto</th>
                    <th class="p-5 text-xs font-black text-rose-400 uppercase tracking-widest border-b border-rose-50 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-rose-50">
                @forelse($promotores as $promotor)
                <tr class="hover:bg-rose-50/10 transition-colors">
                    <td class="p-5">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-xl bg-rose-100 flex items-center justify-center text-rose-600">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <div>
                                <p class="font-bold text-gray-800">{{ $promotor->nombre }}</p>
                                <p class="text-xs text-gray-400 uppercase font-black tracking-tighter">ID: #{{ str_pad($promotor->id, 4, '0', STR_PAD_LEFT) }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="p-5">
                        <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-xs font-bold">
                            {{ $promotor->empresa ?? 'Sin Empresa' }}
                        </span>
                    </td>
                    <td class="p-5">
                        <div class="flex flex-col">
                            <span class="text-sm font-medium text-gray-700"><i class="fas fa-phone text-rose-300 mr-2 text-xs"></i>{{ $promotor->telefono ?? 'N/A' }}</span>
                        </div>
                    </td>
                    <td class="p-5 text-right">
                        <div class="flex justify-end space-x-2">
                            <a href="{{ route('promotores.edit', $promotor) }}" class="p-2 text-blue-500 hover:bg-blue-50 rounded-lg transition-colors" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('promotores.destroy', $promotor) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este promotor?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-rose-500 hover:bg-rose-50 rounded-lg transition-colors" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="p-10 text-center text-gray-400">
                        <i class="fas fa-box-open text-4xl mb-3 block"></i>
                        <p class="font-medium">No hay promotores registrados.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
