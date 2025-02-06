<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\NotaVenta;
use App\Models\ProductoAlmacen;
use Illuminate\Http\Request;

class NotaVentaController extends Controller
{
    public function index()
    {
        $productos = ProductoAlmacen::with(['producto'])->get();
        $notaVentas = NotaVenta::with(['cliente', 'usuario',"pago"])->orderBy('id','desc')->get();
        $clientes = Cliente::all();
        return view('notaVenta.index', compact("clientes", 'productos', 'notaVentas'));
    }

    public function show($id)
    {
        $notaVentas = NotaVenta::findOrFail($id);
        return response()->json($notaVentas);
    }



    public function update($id)
    {

        $notaVentas = NotaVenta::find($id);

        if (!$notaVentas) {
            return response()->json(['success' => false, 'message' => 'Nota de venta no encontrada'], 404);
        }

        $notaVentas->update(['estado' => 2]);
        return response()->json(['success' => true, 'message' => 'Nota de venta anulada con éxito']);
    }
}
