<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Promotor;
use Illuminate\Http\Request;
use App\Traits\LogsActivity;

class ProductoController extends Controller
{
    use LogsActivity;
    public function index(Request $request)
    {
        $query = Producto::with('promotor');

        // Búsqueda insensible a mayúsculas/minúsculas en TODOS los campos del producto y proveedor
        if ($request->filled('search')) {
            $search = trim($request->search);
            $searchLower = mb_strtolower($search, 'UTF-8');
            $query->where(function($q) use ($searchLower) {
                $q->whereRaw('LOWER(nombre) LIKE ?', ["%{$searchLower}%"])
                  ->orWhereRaw('LOWER(codigo) LIKE ?', ["%{$searchLower}%"])
                  ->orWhereRaw('LOWER(COALESCE(descripcion, \'\')) LIKE ?', ["%{$searchLower}%"])
                  ->orWhereRaw('CAST(precio_venta AS text) LIKE ?', ["%{$searchLower}%"])
                  ->orWhereRaw('CAST(precio_compra AS text) LIKE ?', ["%{$searchLower}%"])
                  ->orWhereRaw('CAST(stock AS text) LIKE ?', ["%{$searchLower}%"])
                  ->orWhereHas('promotor', function($qp) use ($searchLower) {
                      $qp->whereRaw('LOWER(nombre) LIKE ?', ["%{$searchLower}%"]);
                  });
            });
        }

        // Filtro por estado de stock
        if ($request->filled('stock_status') || $request->filled('stock')) {
            $stockParam = $request->stock_status ?? $request->stock;
            if ($stockParam == 'critico' || $stockParam == 'bajo') {
                $query->whereColumn('stock', '<=', 'stock_minimo');
            }
        }

        // Filtro por próximos a vencer (30 días)
        if ($request->filled('vencimiento') && $request->vencimiento == 'proximo') {
            $query->whereNotNull('fecha_caducidad')
                  ->where('fecha_caducidad', '<=', now()->addDays(30))
                  ->where('fecha_caducidad', '>=', now());
        }

        $productos = $query->latest()->get();

        // Registrar consulta en bitácora si hay parámetros
        if ($request->anyFilled(['search', 'stock_status', 'vencimiento'])) {
            $this->logActivity('QUERY', "Consulta de stock realizada", $request->all());
        }

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
            'codigo' => 'nullable|string|unique:productos,codigo',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'precio_compra' => 'required|numeric|min:0',
            'precio_venta' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'stock_minimo' => 'required|integer|min:0',
            'fecha_caducidad' => 'nullable|date',
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

        $this->logActivity('CREATE', "Producto creado: {$request->nombre}", $data);

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
            'codigo' => "nullable|string|unique:productos,codigo,{$producto->id}",
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'precio_compra' => 'required|numeric|min:0',
            'precio_venta' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'stock_minimo' => 'required|integer|min:0',
            'fecha_caducidad' => 'nullable|date',
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

        $oldData = $producto->toArray();
        $producto->update($data);

        $this->logActivity('UPDATE', "Producto actualizado: {$request->nombre}", [
            'old' => $oldData,
            'new' => $producto->fresh()->toArray()
        ]);

        return redirect()->route('productos.index')->with('success', 'Producto actualizado exitosamente.');
    }

    public function destroy(Producto $producto)
    {
        $productData = $producto->toArray();
        $nombre = $producto->nombre;
        $producto->delete();
        
        $this->logActivity('DELETE', "Producto eliminado: {$nombre}", $productData);
        
        return redirect()->route('productos.index')->with('success', 'Producto eliminado exitosamente.');
    }
}
