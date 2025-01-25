<?php

namespace App\Http\Controllers;

use App\Models\Servicio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ServicioController extends Controller
{
    public function index()
    {
        $servicios = Servicio::all();
        return view('servicios.index', compact('servicios'));
    }

    public function show($id)
    {
        $mascota = Servicio::findOrFail($id);
        return response()->json($mascota);
    }

    public function store(Request $request)
    {
        $request->validate([

            'nombre' => 'required|string|max:255',

            'precio' => 'required|string|max:50',
            'descripcion' => 'required|string|max:100',

        ]);

        $data = $request->all();



        Servicio::create($data);
        return response()->json(['success' => true, 'message' => 'Servicio registrada con éxito']);
    }

    public function update(Request $request, $id)
    {
        // dd($request);
        $mascota = Servicio::find($id);

        $request->validate([
            'nombre' => 'required|string|max:255',

            'precio' => 'required|string|max:50',
            'descripcion' => 'required|string|max:100',
        ]);

        $data = $request->all();



        $mascota->update($data);
        return response()->json(['success' => true, 'message' => 'Servicio actualizada con éxito']);
    }

    public function destroy($id)
    {
        $mascota = Servicio::findOrFail($id);

        $mascota->delete();
        return response()->json(['success' => true, 'message' => 'Servicio eliminada con éxito']);
    }
}
