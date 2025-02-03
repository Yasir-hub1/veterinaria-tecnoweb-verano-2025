<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerificarPermiso
{
    /**
     * Maneja la verificación de permisos para la petición entrante.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    // public function handle(Request $request, Closure $next, $permiso)
    // {
    //     // Verificar que el permiso no sea nulo
    //     if (!$permiso) {
    //         return response()->json([
    //             'message' => 'Error de configuración: Permiso no especificado',
    //             'error' => 'Configuration Error'
    //         ], 500);
    //     }

    //     // Verificar que el usuario esté autenticado
    //     if (!$request->user()) {
    //         return response()->json([
    //             'message' => 'User no autenticado',
    //             'error' => 'Unauthorized'
    //         ], 401);
    //     }

    //     // Verificar el permiso del usuario
    //     if (!$request->user()->tienePermiso($permiso)) {
    //         return response()->json([
    //             'message' => 'No tiene los permisos necesarios para acceder a este recurso',
    //             'error' => 'Forbidden',
    //             'required_permission' => $permiso
    //         ], 403);
    //     }

    //     return $next($request);
    // }

}
