<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\NotaVenta;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class ReporteController extends Controller
{
    public function index()
    {
        $clientes = Cliente::select('id', DB::raw("CONCAT(nombre, ' ', COALESCE(apellido, '')) as nombre_completo"))
            ->orderBy('nombre')
            ->get();

        $usuarios = Usuario::select('id', 'name')
            ->orderBy('name')
            ->get();

        return view('reportes.index', compact('clientes', 'usuarios'));
    }

    public function generarReporte(Request $request)
    {
        $request->validate([
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'cliente_id' => 'nullable|exists:clientes,id',
            'usuario_id' => 'nullable|exists:usuarios,id',
        ]);

        $query = NotaVenta::with(['cliente', 'usuario'])
            ->whereBetween('fecha', [
                Carbon::parse($request->fecha_inicio)->startOfDay(),
                Carbon::parse($request->fecha_fin)->endOfDay()
            ]);

        if ($request->cliente_id) {
            $query->where('cliente_id', $request->cliente_id);
        }

        if ($request->usuario_id) {
            $query->where('usuario_id', $request->usuario_id);
        }

        $ventas = $query->orderBy('fecha', 'desc')->get();

        // Calcular totales
        $resumen = [
            'total_ventas' => $ventas->count(),
            'monto_total' => $ventas->sum('total'),
            'promedio_venta' => $ventas->avg('total'),
            'venta_minima' => $ventas->min('total'),
            'venta_maxima' => $ventas->max('total'),
        ];

        // Agrupar por método de pago
        $pagos_por_tipo = $ventas->groupBy('tipopago')
            ->map(function ($grupo) {
                return [
                    'cantidad' => $grupo->count(),
                    'total' => $grupo->sum('total')
                ];
            });

        if ($request->ajax()) {
            return response()->json([
                'ventas' => $ventas,
                'resumen' => $resumen,
                'pagos_por_tipo' => $pagos_por_tipo
            ]);
        }

        return view('reportes.index', compact('ventas', 'resumen', 'pagos_por_tipo'));
    }

    public function exportar(Request $request)
    {
        // Validar parámetros
        $request->validate([
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'cliente_id' => 'nullable|exists:clientes,id',
            'usuario_id' => 'nullable|exists:usuarios,id',
        ]);

        $query = NotaVenta::with(['cliente', 'usuario'])
            ->whereBetween('fecha', [
                Carbon::parse($request->fecha_inicio)->startOfDay(),
                Carbon::parse($request->fecha_fin)->endOfDay()
            ]);

        if ($request->cliente_id) {
            $query->where('cliente_id', $request->cliente_id);
        }

        if ($request->usuario_id) {
            $query->where('usuario_id', $request->usuario_id);
        }

        $ventas = $query->orderBy('fecha', 'desc')->get();

        // Generar nombre del archivo
        $fileName = 'reporte_ventas_' . date('Y-m-d_His') . '.csv';

        // Crear el archivo CSV
        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );

        $columns = array('ID', 'Fecha', 'Cliente', 'Usuario', 'Tipo Pago', 'Total', 'Estado');

        $callback = function() use ($ventas, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($ventas as $venta) {
                fputcsv($file, array(
                    $venta->id,
                    $venta->fecha,
                    $venta->cliente->nombre . ' ' . $venta->cliente->apellido,
                    $venta->usuario->name,
                    $venta->tipopago,
                    $venta->total,
                    $venta->estado
                ));
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
