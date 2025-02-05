<?php

use App\Http\Controllers\AjusteInventarioController;
use App\Http\Controllers\AlmacenController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CallBackPagoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MascotaController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ServicioController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\NotaVentaController;
use App\Http\Controllers\OrdenServicioController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\ProductoAlmacenController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\UsuarioRolPermisoController;
use App\Models\UsuarioRolPermiso;

// Redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes
Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'showLoginForm')->name('login');
    Route::post('/login', 'login');
    Route::post('/logout', 'logout')->name('logout');
});

// Protected Routes
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // User Management Routes
    Route::prefix('usuarios')->group(function () {
        Route::controller(UsuarioController::class)->group(function () {
            Route::get('/', 'index')->name('usuarios.index');
        Route::post('/', 'store')->name('usuarios.store');
        Route::get('/{id}', 'show')->where('id', '[0-9]+')->name('usuarios.show');
        Route::put('/{id}', 'update')->where('id', '[0-9]+')->name('usuarios.update');
        Route::delete('/{id}', 'destroy')->where('id', '[0-9]+')->name('usuarios.destroy');


        });
    });

    // Roles Routes
    Route::prefix('roles')->group(function () {
        Route::controller(RoleController::class)->group(function () {
            Route::get('/', 'index')->name('roles.index');
            Route::post('/', 'store')->name('roles.store');
            Route::get('/{role}', 'show')->name('roles.show');
            Route::put('/{role}', 'update')->name('roles.update');
            Route::delete('/{role}', 'destroy')->name('roles.destroy');
        });
    });

    Route::get('/asignacion-roles', [UsuarioRolPermisoController::class, 'index'])->name('asignacion-roles.index');
    Route::get('/asignacion-roles/{id}', [UsuarioRolPermisoController::class, 'show']);
    Route::put('/asignacion-roles/{id}', [UsuarioRolPermisoController::class, 'update']);

Route::prefix('mascotas')->group(function () {
    Route::controller(MascotaController::class)->group(function () {
        Route::get('/', 'index')->name('mascotas.index');
        Route::post('/', 'store')->name('mascotas.store');
        Route::get('/{id}', 'show')->where('id', '[0-9]+')->name('mascotas.show');
        Route::put('/{id}', 'update')->where('id', '[0-9]+')->name('mascotas.update');
        Route::delete('/{id}', 'destroy')->where('id', '[0-9]+')->name('mascotas.destroy');
    });
});

Route::prefix('clientes')->group(function () {
    Route::controller(ClienteController::class)->group(function () {
        Route::get('/', 'index')->name('clientes.index');
    Route::post('/', 'store')->name('clientes.store');
    Route::get('/{id}', 'show')->where('id', '[0-9]+')->name('clientes.show');
    Route::put('/{id}', 'update')->where('id', '[0-9]+')->name('clientes.update');
    Route::delete('/{id}', 'destroy')->where('id', '[0-9]+')->name('clientes.destroy');


    });
});

Route::prefix('almacenes')->group(function () {
    Route::controller(AlmacenController::class)->group(function () {
        Route::get('/', 'index')->name('almacenes.index');
        Route::post('/', 'store')->name('almacenes.store');
        Route::get('/{id}', 'show')->where('id', '[0-9]+')->name('almacenes.show');
        Route::put('/{id}', 'update')->where('id', '[0-9]+')->name('almacenes.update');
        Route::delete('/{id}', 'destroy')->where('id', '[0-9]+')->name('almacenes.destroy');
    });
});

    // Product Management Routes
    Route::prefix('productos')->group(function () {
        Route::controller(ProductoController::class)->group(function () {
            Route::get('/', 'index')->name('productos.index');
            Route::post('/', 'store')->name('productos.store');
            Route::get('/{producto}', 'show')->name('productos.show');
            Route::put('/{producto}', 'update')->name('productos.update');
            Route::delete('/{producto}', 'destroy')->name('productos.destroy');
        });
    });

    Route::prefix('inventarios')->group(function () {
        Route::controller(ProductoAlmacenController::class)->group(function () {
            Route::get('/', 'index')->name('inventarios.index');
            Route::post('/', 'store')->name('inventarios.store');
            Route::get('/{producto}', 'show')->name('inventarios.show');
            Route::put('/{producto}', 'update')->name('inventarios.update');
            Route::delete('/{producto}', 'destroy')->name('inventarios.destroy');
        });
    });

    Route::prefix('ajusteInventarios')->group(function () {
        Route::controller(AjusteInventarioController::class)->group(function () {
            Route::get('/', 'index')->name('ajusteInventarios.index');
            Route::post('/', 'store')->name('ajusteInventarios.store');
            Route::get('/{producto}', 'show')->name('ajusteInventarios.show');
            Route::put('/{producto}', 'update')->name('ajusteInventarios.update');
            Route::delete('/{producto}', 'destroy')->name('ajusteInventarios.destroy');
        });
    });


    Route::prefix('servicios')->group(function () {
        Route::controller(ServicioController::class)->group(function () {
            Route::get('/', 'index')->name('servicios.index');
            Route::post('/', 'store')->name('servicios.store');
            Route::get('/{servicio}', 'show')->name('servicios.show');
            Route::put('/{servicio}', 'update')->name('servicios.update');
            Route::delete('/{servicio}', 'destroy')->name('servicios.destroy');
        });
    });



     Route::prefix('ordenServicios')->group(function () {
        Route::controller(OrdenServicioController::class)->group(function () {
            Route::get('/', 'index')->name('ordenServicios.index');
            // Route::post('/', 'store')->name('ordenServicios.store');
            // Route::get('/{servicio}', 'show')->name('ordenServicios.show');
            Route::put('/{servicio}', 'update')->name('ordenServicios.update');
            // Route::delete('/{servicio}', 'destroy')->name('ordenServicios.destroy');
        });
    });

    Route::prefix('pagos')->group(function () {
        Route::controller(PagoController::class)->group(function () {
            Route::get('/', 'index')->name('pagos.index');
            Route::post('/', 'store')->name('pagos.store');
            Route::get('/{servicio}', 'show')->name('pagos.show');
            Route::put('/{servicio}', 'update')->name('pagos.update');
            Route::delete('/{servicio}', 'destroy')->name('pagos.destroy');
            Route::post('/generarCobro', 'generarCobro')->name('pagos.generarCobro');
            Route::post('/generarCobroVenta', 'generarCobroVenta')->name('pagos.generarCobro.venta');// para ventas
        });
    });

    // PAGOS WEB
    Route::post('/callback', CallBackPagoController::class)->name('pagos.callback');


    Route::prefix('notaVentas')->group(function () {
        Route::controller(NotaVentaController::class)->group(function () {
            Route::get('/', 'index')->name('notaVentas.index');
            Route::post('/', 'store')->name('notaVentas.store');
            Route::get('/{servicio}', 'show')->name('notaVentas.show');
            Route::put('/{servicio}', 'update')->name('notaVentas.update');
            Route::delete('/{servicio}', 'destroy')->name('notaVentas.destroy');
        });
    });

    Route::prefix('reportes')->group(function () {
        Route::get('/ventas', [ReporteController::class, 'index'])->name('reportes.index');
        Route::post('/generar', [ReporteController::class, 'generarReporte'])->name('reportes.generar');
        Route::get('/exportar', [ReporteController::class, 'exportarExcel'])->name('reportes.exportar');
    });

    Route::prefix('reportesOrdenServicio')->group(function () {
        Route::get('/orden', [OrdenServicioController::class, 'reporte'])->name('reportesOrdenServicio.index');
        Route::post('/generarOrden', [OrdenServicioController::class, 'generarReporte'])->name('reportesOrdenServicio.generar');
        Route::get('/exportar', [OrdenServicioController::class, 'exportarExcel'])->name('reportesOrdenServicio.exportar');
    });
});
