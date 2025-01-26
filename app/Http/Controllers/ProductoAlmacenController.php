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
            'stock' => 'required|string|max:100',

        ]);

        $data = $request->all();

        ProductoAlmacen::create($data);
        return response()->json(['success' => true, 'message' => 'Ingreso de inventario registrada con éxito']);
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
