<?php

namespace App\Http\Controllers;

use App\Models\Mascota;
use App\Models\NotaServicio;
use App\Models\NotaVenta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $totalMascotas = Mascota::count();

        // Usando el nombre correcto de la tabla
        $ventasHoy = DB::table('notas_venta')
            ->whereDate('fecha', today())
            ->sum('total');

        $citasPendientes = DB::table('orden_servicio')
            ->where('estado', 'pendiente')
            ->count();

        return view('dashboard.dashboard', compact('totalMascotas', 'ventasHoy', 'citasPendientes'));
        // return view('dashboard.dashboard');

    }
}
