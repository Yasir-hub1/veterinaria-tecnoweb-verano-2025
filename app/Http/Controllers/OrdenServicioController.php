<?php

namespace App\Http\Controllers;

use App\Models\Mascota;
use App\Models\OrdenServicio;
use App\Models\Servicio;
use Illuminate\Http\Request;

class OrdenServicioController extends Controller
{
    public function index()
    {
        $servicios = Servicio::all();
        $ordenServicios = OrdenServicio::with(['mascota', 'usuario'])->get();
        $mascotas = Mascota::all();
        return view('ordenServicio.index', compact("mascotas", 'servicios', 'ordenServicios'));
    }

    public function show($id)
    {
        $ordenServicio = OrdenServicio::findOrFail($id);
        return response()->json($ordenServicio);
    }



    public function update($id)
    {

        $ordenServicio = OrdenServicio::find($id);

        if (!$ordenServicio) {
            return response()->json(['success' => false, 'message' => 'Orden no encontrada'], 404);
        }

        $ordenServicio->update(['estado' => 2]);
        return response()->json(['success' => true, 'message' => 'Orden anulada con éxito']);
    }
}
