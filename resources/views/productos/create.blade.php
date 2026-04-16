@extends('layouts.app')

@section('title', 'Nuevo Producto - Salon Anita')

@section('header')
    <div>
        <h2 class="text-3xl font-extrabold text-gray-800 tracking-tight">Nuevo Producto</h2>
        <p class="text-gray-500 font-medium">Registra un nuevo producto en el inventario.</p>
    </div>
@endsection

@section('content')
<div class="max-w-4xl">
    <div class="bg-white rounded-3xl shadow-sm border border-rose-50 overflow-hidden">
        <form action="{{ route('productos.store') }}" method="POST" enctype="multipart/form-data" class="p-8">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Imagen del Producto -->
                <div class="md:col-span-2 space-y-2">
                    <label class="text-xs font-black text-rose-400 uppercase tracking-widest px-1">Fotografía del Producto</label>
                    <div class="flex items-center space-x-6">
                        <div class="w-24 h-24 bg-rose-50 border-2 border-dashed border-rose-200 rounded-3xl flex items-center justify-center text-rose-300" id="preview-container">
                            <i class="fas fa-image text-3xl"></i>
                        </div>
                        <div class="flex-1">
                            <input type="file" name="imagen" accept="image/*" id="imagen-input"
                                class="w-full text-xs text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-[10px] file:font-black file:uppercase file:bg-rose-100 file:text-rose-700 hover:file:bg-rose-200 cursor-pointer">
                            <p class="text-[9px] text-gray-400 mt-2 italic">Recomendado: 500x500px. JPG o PNG, máximo 2MB.</p>
                        </div>
                    </div>
                    @error('imagen') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-2 space-y-2">
                    <label class="text-xs font-black text-rose-400 uppercase tracking-widest px-1">Nombre del Producto</label>
                    <input type="text" name="nombre" value="{{ old('nombre') }}" required 
                        class="w-full bg-rose-50/30 border border-rose-100 rounded-2xl p-4 text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all"
                        placeholder="Ej. Shampoo Post-Keratina 500ml">
                    @error('nombre') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2">
                    <label class="text-xs font-black text-rose-400 uppercase tracking-widest px-1">Precio Compra (Bs.)</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-bold text-xs">Bs.</span>
                        <input type="number" step="0.01" name="precio_compra" value="{{ old('precio_compra') }}" required 
                            class="w-full bg-rose-50/30 border border-rose-100 rounded-2xl p-4 pl-12 text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all font-bold"
                            placeholder="0.00">
                    </div>
                    @error('precio_compra') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2">
                    <label class="text-xs font-black text-rose-400 uppercase tracking-widest px-1">Precio de Venta (Bs.)</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-rose-500 font-bold text-xs">Bs.</span>
                        <input type="number" step="0.01" name="precio_venta" value="{{ old('precio_venta') }}" required 
                            class="w-full bg-rose-50/30 border border-rose-100 rounded-2xl p-4 pl-12 text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all font-bold"
                            placeholder="0.00">
                    </div>
                    @error('precio_venta') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2">
                    <label class="text-xs font-black text-rose-400 uppercase tracking-widest px-1">Stock Inicial</label>
                    <input type="number" name="stock" value="{{ old('stock', 0) }}" required 
                        class="w-full bg-rose-50/30 border border-rose-100 rounded-2xl p-4 text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all"
                        placeholder="0">
                    @error('stock') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-1 space-y-2">
                    <label class="text-xs font-black text-rose-400 uppercase tracking-widest px-1">Promotor / Proveedor</label>
                    <select name="promotor_id" 
                        class="w-full bg-rose-50/30 border border-rose-100 rounded-2xl p-4 text-gray-800 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all appearance-none cursor-pointer">
                        <option value="">-- Sin Promotor Relacionado --</option>
                        @foreach($promotores as $promotor)
                            <option value="{{ $promotor->id }}" {{ old('promotor_id') == $promotor->id ? 'selected' : '' }}>
                                {{ $promotor->nombre }} ({{ $promotor->empresa ?? 'Sin Empresa' }})
                            </option>
                        @endforeach
                    </select>
                    @error('promotor_id') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-2 space-y-2">
                    <label class="text-xs font-black text-rose-400 uppercase tracking-widest px-1">Descripción</label>
                    <textarea name="descripcion" rows="3"
                        class="w-full bg-rose-50/30 border border-rose-100 rounded-2xl p-4 text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all"
                        placeholder="Detalles del producto...">{{ old('descripcion') }}</textarea>
                    @error('descripcion') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="mt-10 flex items-center justify-end space-x-4">
                <a href="{{ route('productos.index') }}" class="px-6 py-3 rounded-2xl font-bold text-gray-400 hover:text-gray-600 transition-all">Cancelar</a>
                <button type="submit" class="bg-rose-500 hover:bg-rose-600 text-white px-10 py-3 rounded-2xl font-bold transition-all shadow-lg shadow-rose-100">
                    Registrar Producto
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
