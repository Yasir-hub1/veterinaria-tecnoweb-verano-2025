<?php

use App\Http\Controllers\AlmacenController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CallBackPagoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MascotaController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ServicioController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\OrdenServicioController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\ProductoAlmacenController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UsuarioController;
use App\Models\OrdenServicio;
use App\Models\ProductoAlmacen;

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
            Route::get('/{usuario}', 'show')->name('usuarios.show');
            Route::put('/{usuario}', 'update')->name('usuarios.update');
            Route::delete('/{usuario}', 'destroy')->name('usuarios.destroy');

            // Permission Management
            Route::get('/{usuario}/permisos', 'getPermisos')->middleware('permiso:gestionar_permisos');
            Route::post('/{usuario}/permisos', 'updatePermisos')->middleware('permiso:gestionar_permisos');
        });
    });

    // Roles Routes
    Route::prefix('roles')->group(function () {
        Route::controller(RoleController::class)->group(function () {
            Route::get('/', 'index')->middleware('permiso:ver_roles');
            Route::post('/', 'store')->middleware('permiso:crear_roles');
            Route::get('/{role}', 'show')->middleware('permiso:ver_roles');
            Route::put('/{role}', 'update')->middleware('permiso:editar_roles');
            Route::delete('/{role}', 'destroy')->middleware('permiso:eliminar_roles');
        });
    });

Route::prefix('mascotas')->group(function () {
    Route::controller(MascotaController::class)->group(function () {
        Route::get('/', 'index')->name('mascotas.index');
        Route::post('/', 'store')->name('mascotas.store');
        Route::get('/{id}', 'show')->where('id', '[0-9]+')->name('mascotas.show');
        Route::put('/{id}', 'update')->where('id', '[0-9]+')->name('mascotas.update');
        Route::delete('/{id}', 'destroy')->where('id', '[0-9]+')->name('mascotas.destroy');
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
        });
    });

    // PAGOS WEB
    Route::post('/callback', CallBackPagoController::class)->name('pagos.callback');

});
