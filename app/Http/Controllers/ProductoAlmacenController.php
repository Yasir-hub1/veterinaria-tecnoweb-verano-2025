<?php

namespace App\Http\Controllers;

use App\Models\Almacen;
use App\Models\Producto;
use App\Models\ProductoAlmacen;
use Illuminate\Http\Request;

class ProductoAlmacenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $productos = Producto::with('categoria')->get();
    $almacenes = Almacen::all();
    $ingresos = ProductoAlmacen::with(['producto', 'almacen'])->get();

    return view('inventario.index', compact("ingresos", 'productos', 'almacenes'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'almacen_id' => 'required|exists:almacenes,id',
            'stock' => 'required|numeric|max:100',  // Cambié a 'numeric' para asegurar que el stock sea un número
        ]);

        $data = $request->all();

        // Verificar si ya existe un registro con el mismo producto_id y almacen_id
        $productoAlmacen = ProductoAlmacen::where('producto_id', $data['producto_id'])
                                          ->where('almacen_id', $data['almacen_id'])
                                          ->first();

        if ($productoAlmacen) {
            // Si ya existe, sumar el stock recibido al stock actual
            $productoAlmacen->stock += $data['stock']; // Asume que el campo stock es un número
            $productoAlmacen->save();
            return response()->json(['success' => true, 'message' => 'Stock actualizado con éxito']);
        } else {
            // Si no existe, crear un nuevo registro
            ProductoAlmacen::create($data);
            return response()->json(['success' => true, 'message' => 'Ingreso de inventario registrada con éxito']);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $ingreso = ProductoAlmacen::findOrFail($id);
        return response()->json($ingreso);
    }



    public function update(Request $request, $id)
    {
        // dd($request);
        $ingreso = ProductoAlmacen::find($id);

        $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'almacen_id' => 'required|exists:almacenes,id',
            'stock' => 'required|string|max:100',

        ]);

        $data = $request->all();


        $ingreso->update($data);
        return response()->json(['success' => true, 'message' => 'Ingreso actualizado con éxito']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $ingreso = ProductoAlmacen::findOrFail($id);

        $ingreso->delete();
        return response()->json(['success' => true, 'message' => 'Ingreso eliminado con éxito']);
    }
}
