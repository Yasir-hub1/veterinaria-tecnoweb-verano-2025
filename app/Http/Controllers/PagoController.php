<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\DetalleServicio;
use App\Models\Mascota;
use App\Models\NotaVenta;
use App\Models\OrdenServicio;
use App\Models\Pago;
use App\Models\Usuario;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;

use function Laravel\Prompts\table;

class PagoController extends Controller
{
    public function generarCobro(Request $request)
    {
    //    dd($request->all());
       $nroTransaccion = 0;
       $mascota=Mascota::find($request->mascotaId);

       $cliente = $this->getClienteByMascota($mascota->id);

            do {
            $nroPago = rand(100000, 999999);
            $existe = Pago::where('id', $nroPago)->exists();
        } while ($existe);
        try {
            $lcComerceID           = "d029fa3a95e174a19934857f535eb9427d967218a36ea014b70ad704bc6c8d1c";  // credencia dado por pagofacil
            $lnMoneda              = 1;
            $lnTelefono            = $cliente->celular;
            $lcNombreUsuario       = auth()->user()->id;
            $lnCiNit               = $cliente->nit;
            $lcNroPago             = $nroPago; // Genera un número aleatorio entre 100,000 y 999,999   sirve para callback , pedidoID
            $lnMontoClienteEmpresa = $request->tnMonto;
            $lcCorreo              = $cliente->email;
            $lcUrlCallBack         = route('pagos.callback');
            $lcUrlReturn           = "http://localhost:8000";
            $laPedidoDetalle       =  $request->taPedidoDetalle;
            $lcUrl                 = "";

            $loClient = new Client();

            if ($request->tnTipoServicio == 1) {
                $lcUrl = "https://serviciostigomoney.pagofacil.com.bo/api/servicio/generarqrv2";
            } elseif ($request->tnTipoServicio == 2) {
                $lcUrl = "https://serviciostigomoney.pagofacil.com.bo/api/servicio/realizarpagotigomoneyv2";
            }

            $laHeader = [
                'Accept' => 'application/json'
            ];

            $laBody   = [
                "tcCommerceID"          => $lcComerceID,
                "tnMoneda"              => $lnMoneda,
                "tnTelefono"            => $lnTelefono,
                'tcNombreUsuario'       => $lcNombreUsuario,
                'tnCiNit'               => $lnCiNit,
                'tcNroPago'             => $lcNroPago,
                "tnMontoClienteEmpresa" => $lnMontoClienteEmpresa,
                "tcCorreo"              => $lcCorreo,
                'tcUrlCallBack'         => $lcUrlCallBack,
                "tcUrlReturn"           => $lcUrlReturn,

            ];

            $loResponse = $loClient->post($lcUrl, [
                'headers' => $laHeader,
                'json' => $laBody
            ]);

            $laResult = json_decode($loResponse->getBody()->getContents());
            //  var_dump($laResult);
            if ($request->tnTipoServicio == 1) {

                $csrfToken = csrf_token();
                $laValues = explode(";", $laResult->values)[1];
                $nroTransaccion = explode(";", $laResult->values)[0];



               $orden= OrdenServicio::create([

                   "mascota_id"=>$request->mascotaId,
                   "usuario_id"=>auth()->user()->id,
                    'fecha' => now(),
                    'estado' => 1,
                    'total' => $request->tnMonto,
                    'tipopago' => $request->tnTipoServicio,
                ]);

                Pago::create([

                    "orden_servicio_id"=>$orden->id,
                    'fechapago' => now(),
                    'estado' => 1,
                    'tipopago' => $request->tnTipoServicio,
                ]);


                foreach ($laPedidoDetalle as $detalle) {
                    DB::table('detalles_servicio')->insert([
                        'orden_servicio_id' => $orden->id,
                        'servicio_id' =>  $detalle['id'],
                    ]);
                }


                $laQrImage = "data:image/png;base64," . json_decode($laValues)->qrImage;

                return response()->json([
                    'qrImage' => $laQrImage,
                    'nroTransaccion' =>  $nroTransaccion,
                ]);



            } elseif ($request->tnTipoServicio == 2) {
                $orden= OrdenServicio::create([

                    "mascota_id"=>$request->mascotaId,
                    "usuario_id"=>auth()->user()->id,
                     'fecha' => now(),
                     'estado' => 1,
                     'total' => $request->tnMonto,
                     'tipopago' => $request->tnTipoServicio,
                 ]);

                 Pago::create([

                     "orden_servicio_id"=>$orden->id,
                     'fechapago' => now(),
                     'estado' => 1,
                     'tipopago' => $request->tnTipoServicio,
                 ]);


                 foreach ($laPedidoDetalle as $detalle) {
                     DB::table('detalles_servicio')->insert([
                         'orden_servicio_id' => $orden->id,
                         'servicio_id' =>  $detalle['id'],
                     ]);
                 }
            }


        } catch (\Throwable $th) {

            return $th->getMessage() . " - " . $th->getLine();
        }
    }

    private function getClienteByMascota($mascotaId)
    {
        return Cliente::whereHas('mascotas', function ($query) use ($mascotaId) {
            $query->where('id', $mascotaId);
        })->first();
    }


    public function generarCobroVenta(Request $request)
    {
    //    dd($request->all());
       $nroTransaccion = 0;
       $cliente=Cliente::find($request->clienteId);



            do {
            $nroPago = rand(100000, 999999);
            $existe = Pago::where('id', $nroPago)->exists();
        } while ($existe);
        try {
            $lcComerceID           = "d029fa3a95e174a19934857f535eb9427d967218a36ea014b70ad704bc6c8d1c";  // credencia dado por pagofacil
            $lnMoneda              = 1;
            $lnTelefono            = $cliente->celular;
            $lcNombreUsuario       = auth()->user()->id;
            $lnCiNit               = $cliente->nit;
            $lcNroPago             = $nroPago; // Genera un número aleatorio entre 100,000 y 999,999   sirve para callback , pedidoID
            $lnMontoClienteEmpresa = $request->tnMonto;
            $lcCorreo              = $cliente->email;
            $lcUrlCallBack         = route('pagos.callback');
            $lcUrlReturn           = "http://localhost:8000";
            $laPedidoDetalle       =  $request->taPedidoDetalle;
            $lcUrl                 = "";

            $loClient = new Client();

            if ($request->tnTipoServicio == 1) {
                $lcUrl = "https://serviciostigomoney.pagofacil.com.bo/api/servicio/generarqrv2";
            } elseif ($request->tnTipoServicio == 2) {
                $lcUrl = "https://serviciostigomoney.pagofacil.com.bo/api/servicio/realizarpagotigomoneyv2";
            }

            $laHeader = [
                'Accept' => 'application/json'
            ];

            $laBody   = [
                "tcCommerceID"          => $lcComerceID,
                "tnMoneda"              => $lnMoneda,
                "tnTelefono"            => $lnTelefono,
                'tcNombreUsuario'       => $lcNombreUsuario,
                'tnCiNit'               => $lnCiNit,
                'tcNroPago'             => $lcNroPago,
                "tnMontoClienteEmpresa" => $lnMontoClienteEmpresa,
                "tcCorreo"              => $lcCorreo,
                'tcUrlCallBack'         => $lcUrlCallBack,
                "tcUrlReturn"           => $lcUrlReturn,

            ];

            $loResponse = $loClient->post($lcUrl, [
                'headers' => $laHeader,
                'json' => $laBody
            ]);

            $laResult = json_decode($loResponse->getBody()->getContents());
            //  var_dump($laResult);
            if ($request->tnTipoServicio == 1) {

                $csrfToken = csrf_token();
                $laValues = explode(";", $laResult->values)[1];
                $nroTransaccion = explode(";", $laResult->values)[0];



               $notaVenta= NotaVenta::create([

                   "cliente_id"=>$request->clienteId,
                   "usuario_id"=>auth()->user()->id,
                    'fecha' => now(),
                    'estado' => 1,
                    'total' => $request->tnMonto,
                    'tipopago' => $request->tnTipoServicio,
                ]);

                Pago::create([

                    "nota_venta_id"=>$notaVenta->id,
                    'fechapago' => now(),
                    'estado' => 1,
                    'tipopago' => $request->tnTipoServicio,
                ]);


                foreach ($laPedidoDetalle as $detalle) {
                    DB::table('detalles_venta')->insert([
                        'nota_venta_id' => $notaVenta->id,
                        'producto_id' =>  $detalle['id'],
                        'cantidad' =>  $detalle['cantidad'],
                        'total' =>  $detalle['subtotal'],
                    ]);
                }


                $laQrImage = "data:image/png;base64," . json_decode($laValues)->qrImage;

                return response()->json([
                    'qrImage' => $laQrImage,
                    'nroTransaccion' =>  $nroTransaccion,
                ]);



            } elseif ($request->tnTipoServicio == 2) {
                $notaVenta= NotaVenta::create([

                    "cliente_id"=>$request->clienteId,
                    "usuario_id"=>auth()->user()->id,
                     'fecha' => now(),
                     'estado' => 1,
                     'total' => $request->tnMonto,
                     'tipopago' => $request->tnTipoServicio,
                 ]);

                 Pago::create([

                     "nota_venta_id"=>$notaVenta->id,
                     'fechapago' => now(),
                     'estado' => 1,
                     'tipopago' => $request->tnTipoServicio,
                 ]);


                 foreach ($laPedidoDetalle as $detalle) {
                    DB::table('detalles_venta')->insert([
                        'nota_venta_id' => $notaVenta->id,
                        'producto_id' =>  $detalle['id'],
                        'cantidad' =>  $detalle['cantidad'],
                        'total' =>  $detalle['subtotal'],
                    ]);
                }

                 return response()->json([

                    'nroTransaccion' =>  $nroTransaccion,
                ]);
            }


        } catch (\Throwable $th) {

            return $th->getMessage() . " - " . $th->getLine();
        }
    }




}
