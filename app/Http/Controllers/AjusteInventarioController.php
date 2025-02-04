<?php

namespace App\Http\Controllers;

use App\Models\AjusteInventario;
use App\Models\DestalleAjuste;
use App\Models\ProductoAlmacen;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AjusteInventarioController extends Controller
{
    public function index()
    {
        $ajustes = AjusteInventario::with([
            'usuario', // Usuario que realizó el ajuste
            'detalles.productoAlmacen.producto' // Producto ajustado a través de ProductoAlmacen
        ])->get();

    $ingresos = ProductoAlmacen::with(['producto', 'almacen'])->get();

    return view('egresoInventario.index', compact("ingresos", 'ajustes'));
    }


    public function store(Request $request)
    {
        // Validación inicial de campos
        $request->validate([
            'ingreso_id' => 'required|integer',
            'tipo' => 'required|integer',
            'glosa' => 'required|string|max:255',
            'stock' => 'required|numeric|min:1'
        ]);

        // Convertir tipo a entero (por seguridad)
        $tipo = (int) $request->tipo;

        return DB::transaction(function () use ($request, $tipo) {
            // Obtener el producto de almacén
            $productoAlmacen = ProductoAlmacen::findOrFail($request->ingreso_id);

            if ($tipo === 1) { // Egreso de inventario
                // Verificar si hay suficiente stock
                if ($productoAlmacen->stock < $request->stock) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Stock insuficiente. Stock disponible: ' . $productoAlmacen->stock
                    ], 422);
                }

                // Restar el stock
                $productoAlmacen->stock -= $request->stock;
            } else { // Ingreso de inventario
                // Sumar el stock
                $productoAlmacen->stock += $request->stock;
            }

            // Guardar la actualización de stock
            $productoAlmacen->save();

            // Crear el ajuste de inventario
            $ajuste = AjusteInventario::create([
                'usuario_id' => auth()->id(),
                'tipo' => $tipo,
                'fecha' => Carbon::now(),
                'glosa' => $request->glosa
            ]);

            // Crear el detalle del ajuste
            DB::table('detalles_ajuste')->insert([
                'ajuste_id' => $ajuste->id,
                'producto_id' => $productoAlmacen->id,
                'cantidad' => $request->stock
            ]);

            return response()->json([
                'success' => true,
                'message' => ($tipo === 1) ? 'Egreso de inventario registrado con éxito' : 'Ingreso de inventario registrado con éxito'
            ]);
        });
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
