<?php

namespace App\Http\Controllers;

use App\Models\Mascota;
use App\Models\OrdenServicio;
use App\Models\Servicio;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
class OrdenServicioController extends Controller
{
    public function index()
    {
        $servicios = Servicio::all();
        $ordenServicios = OrdenServicio::with(['mascota', 'usuario',"pago"])->orderBy('id','desc')->get();
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

    // public function reporte()
    // {
    //     $mascotas = Mascota::select('id', DB::raw("CONCAT(nombre, ' ', COALESCE(tipo, '')) as nombre_completo"))
    //         ->orderBy('nombre')
    //         ->get();

    //     $usuarios = User::select('id', 'name')
    //         ->orderBy('name')
    //         ->get();

    //     return view('reportes.ordenServicio', compact('mascotas', 'usuarios'));
    // }
    public function reporte()
    {
        $mascotas = Mascota::with('cliente')
            ->select('id', 'nombre', 'cliente_id')
            ->get()
            ->map(function($mascota) {
                return [
                    'id' => $mascota->id,
                    'nombre_completo' => $mascota->nombre . ' - ' .
                                       $mascota->cliente->nombre . ' ' .
                                       $mascota->cliente->apellido
                ];
            });

        $tiposPago = [
            ['id' => '1', 'nombre' => 'QR'],
            ['id' => '2', 'nombre' => 'Tigo Money'],
            ['id' => '3', 'nombre' => 'Efectivo']
        ];

        return view('reportes.ordenServicio', compact('mascotas', 'tiposPago'));
    }

    public function generarReporte(Request $request)
    {
        try {
            $request->validate([
                'fecha_inicio' => 'required|date',
                'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
                'mascota_id' => 'nullable|exists:mascotas,id',
            ]);

            $query = OrdenServicio::with(['mascota.cliente', 'usuario', 'pago'])
                ->whereBetween('fecha', [
                    Carbon::parse($request->fecha_inicio)->startOfDay(),
                    Carbon::parse($request->fecha_fin)->endOfDay()
                ]);

            if ($request->mascota_id) {
                $query->where('mascota_id', $request->mascota_id);
            }

            $servicios = $query->orderBy('fecha', 'desc')->get();

            // Análisis por día
            $serviciosPorDia = $servicios->groupBy(function($servicio) {
                return Carbon::parse($servicio->fecha)->format('Y-m-d');
            })->map(function($grupo) {
                return [
                    'fecha' => $grupo->first()->fecha,
                    'total' => $grupo->sum('total'),
                    'cantidad' => $grupo->count()
                ];
            })->values();

            // Análisis por mascota
            $serviciosPorMascota = $servicios->groupBy('mascota_id')
                ->map(function($grupo) {
                    return [
                        'mascota' => $grupo->first()->mascota->nombre,
                        'cliente' => $grupo->first()->mascota->cliente->nombre . ' ' .
                                   $grupo->first()->mascota->cliente->apellido,
                        'total' => $grupo->sum('total'),
                        'cantidad' => $grupo->count()
                    ];
                })
                ->sortByDesc('total')
                ->take(5)
                ->values();

            // Análisis por tipo
            $serviciosPorTipo = $servicios->groupBy('mascota.tipo')
                ->map(function($grupo) {
                    return [
                        'tipo' => $grupo->first()->mascota->tipo,
                        'total' => $grupo->sum('total'),
                        'cantidad' => $grupo->count()
                    ];
                })->values();

            $resumen = [
                'total_servicios' => $servicios->count(),
                'monto_total' => $servicios->sum('total'),
                'promedio_servicio' => $servicios->avg('total'),
                'servicio_minimo' => $servicios->min('total'),
                'servicio_maximo' => $servicios->max('total'),
                'total_mascotas' => $servicios->unique('mascota_id')->count(),
                'servicios_pendientes' => $servicios->whereNull('pago')->count()
            ];

            return response()->json([
                'servicios' => $servicios,
                'resumen' => $resumen,
                'graficos' => [
                    'servicios_por_dia' => $serviciosPorDia,
                    'servicios_por_mascota' => $serviciosPorMascota,
                    'servicios_por_tipo' => $serviciosPorTipo
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
