<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\NotaVenta;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
class ReporteController extends Controller
{
    public function index()
    {
        $clientes = Cliente::select('id', DB::raw("CONCAT(nombre, ' ', COALESCE(apellido, '')) as nombre_completo"))
            ->orderBy('nombre')
            ->get();

        $tiposPago = [
            ['id' => '1', 'nombre' => 'QR'],
            ['id' => '2', 'nombre' => 'Tigo Money'],
            ['id' => '3', 'nombre' => 'Efectivo']
        ];

        return view('reportes.index', compact('clientes', 'tiposPago'));
    }

    public function generarReporte(Request $request)
    {
        try {
            $request->validate([
                'fecha_inicio' => 'required|date',
                'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
                'cliente_id' => 'nullable|exists:clientes,id',
                'tipo_pago' => 'nullable|in:1,2,3'
            ]);

            $query = NotaVenta::with(['cliente', 'pago'])
                ->whereBetween(DB::raw('DATE(fecha)'), [
                    $request->fecha_inicio,
                    $request->fecha_fin
                ]);

            if ($request->cliente_id) {
                $query->where('cliente_id', $request->cliente_id);
            }

            if ($request->tipo_pago) {
                $query->whereHas('pago', function($q) use ($request) {
                    $q->where('tipopago', $request->tipo_pago);
                });
            }

            $ventas = $query->orderBy('fecha', 'desc')->get();

            // Preparar datos para gráficos
            $ventasPorDia = $this->prepararVentasPorDia($ventas);
            $ventasPorCliente = $this->prepararVentasPorCliente($ventas);
            $ventasPorTipoPago = $this->prepararVentasPorTipoPago($ventas);

            return response()->json([
                'ventas' => $ventas,
                'resumen' => $this->generarResumen($ventas),
                'graficos' => [
                    'ventas_por_dia' => $ventasPorDia,
                    'ventas_por_cliente' => $ventasPorCliente,
                    'ventas_por_tipo_pago' => $ventasPorTipoPago
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function prepararVentasPorDia($ventas)
    {
        return $ventas->groupBy(function($venta) {
            return Carbon::parse($venta->fecha)->format('Y-m-d');
        })->map(function($grupo) {
            return [
                'fecha' => $grupo->first()->fecha,
                'total' => $grupo->sum('total'),
                'cantidad' => $grupo->count()
            ];
        })->values();
    }

    private function prepararVentasPorCliente($ventas)
    {
        return $ventas->groupBy('cliente_id')
            ->map(function($grupo) {
                return [
                    'cliente' => $grupo->first()->cliente->nombre . ' ' . $grupo->first()->cliente->apellido,
                    'total' => $grupo->sum('total'),
                    'cantidad' => $grupo->count()
                ];
            })
            ->sortByDesc('total')
            ->take(5)
            ->values();
    }

    private function prepararVentasPorTipoPago($ventas)
    {
        return $ventas->groupBy('pago.tipopago')
            ->map(function($grupo) {
                return [
                    'tipo' => $this->getTipoPagoNombre($grupo->first()->pago->tipopago),
                    'total' => $grupo->sum('total'),
                    'cantidad' => $grupo->count()
                ];
            })->values();
    }

    private function getTipoPagoNombre($tipo)
    {
        return match($tipo) {
            '1' => 'QR',
            '2' => 'Tigo Money',
            '3' => 'Efectivo',
            default => 'Otro'
        };
    }

    private function generarResumen($ventas)
    {
        return [
            'total_ventas' => $ventas->count(),
            'monto_total' => $ventas->sum('total'),
            'promedio_venta' => $ventas->avg('total'),
            'venta_minima' => $ventas->min('total'),
            'venta_maxima' => $ventas->max('total'),
            'total_clientes' => $ventas->unique('cliente_id')->count()
        ];
    }




}
