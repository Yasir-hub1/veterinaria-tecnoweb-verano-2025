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
       $cliente=Cliente::where("id",$mascota["cliente_id"]);
        do {
            $nroPago = rand(100000, 999999);
            $existe = Pago::where('id', $nroPago)->exists();
        } while ($existe);
        try {
            $lcComerceID           = "d029fa3a95e174a19934857f535eb9427d967218a36ea014b70ad704bc6c8d1c";  // credencia dado por pagofacil
            $lnMoneda              = 1;
            $lnTelefono            = "75633544";
            $lcNombreUsuario       = "343";
            $lnCiNit               = "9000034";
            $lcNroPago             = $nroPago; // Genera un número aleatorio entre 100,000 y 999,999   sirve para callback , pedidoID
            $lnMontoClienteEmpresa = $request->tnMonto;
            $lcCorreo              = $request->tcCorreo;
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
            }

            return response()->json([

                'laResult' =>  $laResult,
            ]);
            // } elseif ($request->tnTipoServicio == 2) {
            //   // Buscar usuario por correo
            //   $user = Usuario::where('email', $request->tcCorreo)->first();

            //   if (!$user) {
            //       // Si no existe el usuario, crearlo
            //       $user = Usuario::create([
            //           'name' => $request->name,
            //           'cedula' => $request->cedula,
            //           'celular' => $request->tnTelefono,
            //           'email' => $request->tcCorreo,
            //           'password' => "null",
            //       ]);
            //       // Asignar rol
            //       $rolCliente = Role::where('nombre', 'Rol_Cliente')->first();

            //           // Si no existe el rol, lo creamos
            //           if (!$rolCliente) {
            //               $rolCliente = Role::create([
            //                   'nombre' => 'Rol_Cliente',
            //                   'descripcion' => 'Rol asignado a los clientes para acceder a funcionalidades básicas del sistema',
            //               ]);
            //           }
            //           // Asignar el rol "Rol_Cliente" al usuario
            //           $user->roles()->attach($rolCliente);
            //   }
            //     Pago::create([
            //         'id' =>  $nroPago,
            //         'fechapago' => now(),
            //         'estado' => 1,
            //         'metodopago' => $request->tnTipoServicio,
            //     ]);

            //     Venta::create([
            //         'id' => $nroTransaccion,
            //         'user_id' => $user->id,
            //         'pago_id' => $lcNroPago,
            //         'fecha' => now(),
            //         'metodopago' => $request->tnTipoServicio,
            //         'montototal' => $request->tnMonto,
            //         'estado' => 1,
            //     ]);
            //     foreach ($laPedidoDetalle as $detalle) {
            //         DetalleVenta::create([
            //             'venta_id' => $nroTransaccion,
            //             'producto_id' =>  $detalle['id'],
            //             'cantidad' => $detalle['cantidad'],
            //             'total' =>  $detalle['subtotal'],
            //         ]);
            //     }
            //

        } catch (\Throwable $th) {

            return $th->getMessage() . " - " . $th->getLine();
        }
    }
}
