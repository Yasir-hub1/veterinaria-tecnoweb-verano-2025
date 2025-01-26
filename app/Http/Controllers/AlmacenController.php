<?php

namespace App\Http\Controllers;

use App\Models\Almacen;
use Illuminate\Http\Request;

class AlmacenController extends Controller
{
    public function index()
    {
        $almacenes = Almacen::all();
        return view('almacen.index', compact('almacenes'));
    }

    public function show($id)
    {
        $mascota = Almacen::findOrFail($id);
        return response()->json($mascota);
    }

    public function store(Request $request)
    {
        $request->validate([

            'nombre' => 'required|string|max:255',


            'descripcion' => 'required|string|max:100',

        ]);

        $data = $request->all();



        Almacen::create($data);
        return response()->json(['success' => true, 'message' => 'Almacen registrada con éxito']);
    }

    public function update(Request $request, $id)
    {
        // dd($request);
        $mascota = Almacen::find($id);

        $request->validate([
            'nombre' => 'required|string|max:255',


            'descripcion' => 'required|string|max:100',
        ]);

        $data = $request->all();



        $mascota->update($data);
        return response()->json(['success' => true, 'message' => 'Almacen actualizada con éxito']);
    }

    public function destroy($id)
    {
        $mascota = Almacen::findOrFail($id);

        $mascota->delete();
        return response()->json(['success' => true, 'message' => 'Almacen eliminada con éxito']);
    }
}
