<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductoController extends Controller
{
    public function index()
    {
        $productos = Producto::with('categoria')->get();
        $categorias = Categoria::all();
        return view('productos.index', compact('productos', 'categorias'));
    }

    public function show($id)
    {
        $mascota = Producto::findOrFail($id);
        return response()->json($mascota);
    }

    public function store(Request $request)
    {
        $request->validate([
            'categoria_id' => 'required|exists:categorias,id',
            'nombre' => 'required|string|max:255',

            'descripcion' => 'required|string|max:50',
            'precio' => 'required|string|max:100',
            'imagen' => 'nullable|image|max:2048'
        ]);

        $data = $request->all();

        if ($request->hasFile('imagen')) {
            $data['imagen'] = $request->file('imagen')->store('productos', 'public');
        }

        Producto::create($data);
        return response()->json(['success' => true, 'message' => 'Producto registrada con éxito']);
    }

    public function update(Request $request, $id)
    {
        // dd($request);
        $mascota = Producto::find($id);

        $request->validate([
            'categoria_id' => 'required|exists:categorias,id',
            'nombre' => 'required|string|max:255',

            'descripcion' => 'required|string|max:50',
            'precio' => 'required|string|max:100',
            'imagen' => 'nullable|image|max:2048'
        ]);

        $data = $request->all();

        if ($request->hasFile('imagen')) {
            if ($mascota->imagen) {
                Storage::disk('public')->delete($mascota->imagen);
            }
            $data['imagen'] = $request->file('imagen')->store('productos', 'public');
        }

        $mascota->update($data);
        return response()->json(['success' => true, 'message' => 'Producto actualizada con éxito']);
    }

    public function destroy($id)
    {
        $mascota = Producto::findOrFail($id);
        if ($mascota->imagen) {
            Storage::disk('public')->delete($mascota->imagen);
        }
        $mascota->delete();
        return response()->json(['success' => true, 'message' => 'Producto eliminada con éxito']);
    }

}
