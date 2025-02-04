<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\MascotaController;
use App\Http\Controllers\NotaVentaController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum'])->group(function () {
    // CU01: Gestión de Usuarios
    Route::middleware(['verificar.permiso:gestionar_usuarios'])->group(function () {
        Route::resource('usuarios', UsuarioController::class);
    });

    // CU02: Gestión de Mascotas
    Route::middleware(['verificar.permiso:gestionar_mascotas'])->group(function () {
        Route::resource('mascotas', MascotaController::class);
    });

    // CU03: Gestión de Productos
    Route::middleware(['verificar.permiso:gestionar_productos'])->group(function () {
        Route::resource('productos', ProductoController::class);
    });

    // CU04: Gestión de Servicios
    Route::middleware(['verificar.permiso:gestionar_servicios'])->group(function () {
        Route::resource('servicios', ServicioController::class);
    });

    // CU05: Gestión de Inventario
    Route::middleware(['verificar.permiso:gestionar_inventario'])->group(function () {
        Route::resource('inventario', InventarioController::class);
        Route::post('inventario/ajuste', [InventarioController::class, 'ajustarInventario']);
    });

    // CU06: Gestión de Ventas
    Route::middleware(['verificar.permiso:gestionar_ventas'])->group(function () {
        Route::resource('ventas', NotaVentaController::class);
        Route::post('ventas/{venta}/pago', [NotaVentaController::class, 'registrarPago']);
    });

    // CU07: Gestión de Pagos
    Route::middleware(['verificar.permiso:gestionar_pagos'])->group(function () {
        Route::get('pagos', [NotaVentaController::class, 'pagos']);
        Route::post('pagos/{pago}/confirmar', [NotaVentaController::class, 'confirmarPago']);
    });

    // CU08: Reportes y Estadísticas
    Route::middleware(['verificar.permiso:ver_reporte_ventas'])->group(function () {
        Route::get('reportes/ventas', [ReporteController::class, 'reporteVentas']);
        Route::get('reportes/servicios', [ReporteController::class, 'reporteServicios']);
        Route::get('reportes/inventario', [ReporteController::class, 'reporteInventario']);
    });
});
