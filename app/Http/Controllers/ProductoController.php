<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Promotor;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    public function index()
    {
        $productos = Producto::with('promotor')->latest()->get();
        return view('productos.index', compact('productos'));
    }

    public function create()
    {
        $promotores = Promotor::all();
        return view('productos.create', compact('promotores'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'precio_compra' => 'required|numeric|min:0',
            'precio_venta' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'promotor_id' => 'nullable|exists:promotores,id',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->all();

        if ($request->hasFile('imagen')) {
            $imageName = time().'.'.$request->imagen->extension();
            $request->imagen->move(public_path('uploads/productos'), $imageName);
            $data['imagen'] = 'uploads/productos/'.$imageName;
        }

        Producto::create($data);

        return redirect()->route('productos.index')->with('success', 'Producto registrado exitosamente.');
    }

    public function edit(Producto $producto)
    {
        $promotores = Promotor::all();
        return view('productos.edit', compact('producto', 'promotores'));
    }

    public function update(Request $request, Producto $producto)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'precio_compra' => 'required|numeric|min:0',
            'precio_venta' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'promotor_id' => 'nullable|exists:promotores,id',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->all();

        if ($request->hasFile('imagen')) {
            // Eliminar imagen vieja si existe
            if ($producto->imagen && file_exists(public_path($producto->imagen))) {
                @unlink(public_path($producto->imagen));
            }

            $imageName = time().'.'.$request->imagen->extension();
            $request->imagen->move(public_path('uploads/productos'), $imageName);
            $data['imagen'] = 'uploads/productos/'.$imageName;
        }

        $producto->update($data);

        return redirect()->route('productos.index')->with('success', 'Producto actualizado exitosamente.');
    }

    public function destroy(Producto $producto)
    {
        $producto->delete();
        return redirect()->route('productos.index')->with('success', 'Producto eliminado exitosamente.');
    }
}
