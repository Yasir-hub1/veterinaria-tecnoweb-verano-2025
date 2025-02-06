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

    public function reporte()
    {
        $mascotas = Mascota::select('id', DB::raw("CONCAT(nombre, ' ', COALESCE(tipo, '')) as nombre_completo"))
            ->orderBy('nombre')
            ->get();

        $usuarios = User::select('id', 'name')
            ->orderBy('name')
            ->get();

        return view('reportes.ordenServicio', compact('mascotas', 'usuarios'));
    }

    public function generarReporte(Request $request)
    {

        try {
            $request->validate([
                'fecha_inicio' => 'required|date',
                'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
                'mascota_id' => 'nullable|exists:mascotas,id',
                'usuario_id' => 'nullable|exists:usuarios,id',
            ]);

            $query = OrdenServicio::with(['mascota', 'usuario'])
            ->whereBetween(DB::raw("fecha::timestamp"), [
                Carbon::parse($request->fecha_inicio)->startOfDay(),
                Carbon::parse($request->fecha_fin)->endOfDay()
            ]);


            if ($request->mascota_id) {
                $query->where('mascota_id', $request->mascota_id);
            }

            if ($request->usuario_id) {
                $query->where('usuario_id', $request->usuario_id);
            }

            $ventas = $query->orderBy('fecha', 'desc')->get();

            $resumen = [
                'total_ventas' => $ventas->count(),
                'monto_total' => $ventas->sum('total'),
                'promedio_venta' => $ventas->avg('total'),
                'venta_minima' => $ventas->min('total'),
                'venta_maxima' => $ventas->max('total'),
            ];

            $pagos_por_tipo = $ventas->groupBy('tipopago')
                ->map(function ($grupo) {
                    return [
                        'cantidad' => $grupo->count(),
                        'total' => $grupo->sum('total')
                    ];
                });

            return response()->json([
                'ventas' => $ventas,
                'resumen' => $resumen,
                'pagos_por_tipo' => $pagos_por_tipo
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function exportarExcel(Request $request)
    {
        try {
            $request->validate([
                'fecha_inicio' => 'required|date',
                'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
                'mascota_id' => 'nullable|exists:mascotas,id',
                'usuario_id' => 'nullable|exists:usuarios,id',
            ]);

            $query = OrdenServicio::with(['mascota', 'usuario'])
                ->whereBetween(DB::raw("fecha::timestamp"), [
                    Carbon::parse($request->fecha_inicio)->startOfDay(),
                    Carbon::parse($request->fecha_fin)->endOfDay()
                ]);

            if ($request->mascota_id) {
                $query->where('mascota_id', $request->mascota_id);
            }

            if ($request->usuario_id) {
                $query->where('usuario_id', $request->usuario_id);
            }

            $ventas = $query->orderBy('fecha', 'desc')->get();

            // Crear un objeto Spreadsheet
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Establecer los encabezados de columna
            $sheet->setCellValue('A1', 'ID');
            $sheet->setCellValue('B1', 'Fecha');
            $sheet->setCellValue('C1', 'Mascota');
            $sheet->setCellValue('D1', 'User');
            $sheet->setCellValue('E1', 'Tipo de Pago');
            $sheet->setCellValue('F1', 'Total');
            $sheet->setCellValue('G1', 'Estado');

            // Estilo de los encabezados (negrita, centrado, color de fondo)
            $styleArray = [
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'CCCCCC']],
            ];
            $sheet->getStyle('A1:G1')->applyFromArray($styleArray);

            // Iterar a través de las ventas y escribirlas en el archivo
            $row = 2;
            foreach ($ventas as $venta) {
                // Tipo de pago
                $tipoPago = $venta->tipopago == 1 ? 'QR' : ($venta->tipopago == 2 ? 'Tigo Money' : 'Otro');
                // Estado
                $estado = $venta->estado == 1 ? 'Activo' : 'Inactivo';

                // Escribir datos en las celdas correspondientes
                $sheet->setCellValue("A$row", $venta->id);
                $sheet->setCellValue("B$row", Carbon::parse($venta->fecha)->format('d/m/Y H:i:s'));
                $sheet->setCellValue("C$row", $venta->mascota->nombre . '-' . $venta->mascota->tipo);
                $sheet->setCellValue("D$row", $venta->usuario->name);
                $sheet->setCellValue("E$row", $tipoPago);
                $sheet->setCellValue("F$row", number_format($venta->total, 2));
                $sheet->setCellValue("G$row", $estado);

                $row++;
            }

            // Establecer el ancho de las columnas automáticamente
            foreach (range('A', 'G') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            // Escribir el archivo Excel
            $writer = new Xlsx($spreadsheet);

            // Definir los encabezados para la descarga del archivo
            $fileName = 'reporte_servicio_' . date('Y-m-d_His') . '.xlsx';
            $headers = [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
                'Cache-Control' => 'max-age=0',
            ];

            // Retornar el archivo Excel como respuesta
            return response()->stream(
                function() use ($writer) {
                    $writer->save('php://output');
                },
                200,
                $headers
            );

        } catch (\Exception $e) {
            return back()->with('error', 'Error al exportar: ' . $e->getMessage());
        }
    }
}
