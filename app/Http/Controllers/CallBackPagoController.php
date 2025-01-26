<?php

namespace App\Http\Controllers;

use App\Models\NotaVenta;
use App\Models\Pago;
use Illuminate\Http\Request;

class CallBackPagoController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $pago_id = $request->input("PedidoID");
        $Fecha = $request->input("Fecha");
        $NuevaFecha = date("Y-m-d", strtotime($Fecha));
        $Hora = $request->input("Hora");
        $tipopago = $request->input("tipopago");
        $Estado = $request->input("Estado");
        $Ingreso = true;

        $pago = Pago::findOrFail($pago_id);
        $pago->fechapago = $Fecha;
        $pago->estado = $Estado;
        $pago->tipopago = $tipopago;
        $pago->update();


        $venta= NotaVenta::where('pago_id', $pago->id)
                      ->first();
        $venta->estado =  $Estado;
        $venta->update();


        try {
            $arreglo = ['error' => 0, 'status' => 1, 'message' => "Pago realizado correctamente.", 'values' => true];
        } catch (\Throwable $th) {
            $arreglo = ['error' => 1, 'status' => 1, 'messageSistema' => "[TRY/CATCH] " . $th->getMessage(), 'message' => "No se pudo realizar el pago, por favor intente de nuevo.", 'values' => false];
        }

        return response()->json($arreglo);
    }
    }
