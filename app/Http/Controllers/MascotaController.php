<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Mascota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MascotaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $mascotas = Mascota::with('cliente')->get();
        $clientes = Cliente::all();
        return view('mascotas.index', compact('mascotas', 'clientes'));
    }

    public function show($id)
    {
        $mascota = Mascota::findOrFail($id);
        return response()->json($mascota);
    }

    public function store(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'nombre' => 'required|string|max:255',
            'edad' => 'required|integer|min:0',
            'tipo' => 'required|string|max:50',
            'raza' => 'required|string|max:100',
            'imagen' => 'nullable|image|max:2048'
        ]);

        $data = $request->all();

        if ($request->hasFile('imagen')) {
            $data['imagen'] = $request->file('imagen')->store('mascotas', 'public');
        }

        Mascota::create($data);
        return response()->json(['success' => true, 'message' => 'Mascota registrada con éxito']);
    }

    public function update(Request $request, $id)
    {
        // dd($request);
        $mascota = Mascota::find($id);

        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'nombre' => 'required|string|max:255',
            'edad' => 'required|integer|min:0',
            'tipo' => 'required|string|max:50',
            'raza' => 'required|string|max:100',
            'imagen' => 'nullable|image|max:2048'
        ]);

        $data = $request->all();

        if ($request->hasFile('imagen')) {
            if ($mascota->imagen) {
                Storage::disk('public')->delete($mascota->imagen);
            }
            $data['imagen'] = $request->file('imagen')->store('mascotas', 'public');
        }

        $mascota->update($data);
        return response()->json(['success' => true, 'message' => 'Mascota actualizada con éxito']);
    }

    public function destroy($id)
    {
        $mascota = Mascota::findOrFail($id);
        if ($mascota->imagen) {
            Storage::disk('public')->delete($mascota->imagen);
        }
        $mascota->delete();
        return response()->json(['success' => true, 'message' => 'Mascota eliminada con éxito']);
    }

}
