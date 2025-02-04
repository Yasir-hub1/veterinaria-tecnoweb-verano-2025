<?php

namespace App\Http\Controllers;

use App\Models\Permiso;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('permisos')->get();
    $permisos = Permiso::all();

    // Define las secciones y sus permisos relacionados
    $permisosAgrupados = [
        'Gestión de Usuarios' => [
            'guardar_usuario',
            'editar_usuario',
            'eliminar_usuario',

            'guardar_rol',
            'editar_rol',
            'eliminar_rol',

        ],
        'Gestión de Mascotas' => [
            'guardar_mascota',
            'editar_mascota',
            'eliminar_mascota',

            'guardar_cliente',
            'editar_cliente',
            'eliminar_cliente',


            'guardar_servicio',
            'editar_servicio',
            'eliminar_servicio',


        ],
        'Gestión de Inventario' => [

            'guardar_ajuste_inventario',
            'ver_ajuste_inventario',


            'guardar_producto',
            'editar_producto',
            'eliminar_producto',

            'guardar_almacen',
            'editar_almacen',
            'eliminar_almacen',
        ],

        'Gestión de Ventas' => [
            // aquí los permisos de ventas
            'guardar_venta',

            'eliminar_venta',

            'ver_pago',


            'guardar_orden_servicio',

            'eliminar_orden_servicio'
        ],
        'Gestión de Reportes y estadisticas' => [
            // aquí los permisos de reportes
            'ver_reporte_venta',

        ]
    ];

    // Filtrar permisos por sección
    $seccionesPermisos = [];
    foreach ($permisosAgrupados as $seccion => $permisosNombres) {
        $seccionesPermisos[$seccion] = $permisos->filter(function($permiso) use ($permisosNombres) {
            return in_array($permiso->nombre, $permisosNombres);
        });
    }

    return view('roles.index', compact('roles', 'seccionesPermisos'));
}

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required',
            'permisos' => 'required'
        ]);

        try {
            DB::beginTransaction();

            $role = Role::create([
                'nombre' => $request->nombre
            ]);

            $role->permisos()->attach($request->permisos);

            DB::commit();

            return response()->json([
                'message' => 'Rol creado exitosamente',
                'role' => $role
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al crear el rol'
            ], 500);
        }
    }

    public function show($id)
    {
        $role = Role::with('permisos')->findOrFail($id);
        return response()->json($role);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:roles,nombre,'.$id,
            'permisos' => 'required|array'
        ]);

        try {
            DB::beginTransaction();

            $role = Role::findOrFail($id);
            $role->update([
                'nombre' => $request->nombre
            ]);

            $role->permisos()->sync($request->permisos);

            DB::commit();

            return response()->json([
                'message' => 'Rol actualizado exitosamente',
                'role' => $role
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al actualizar el rol'
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $role = Role::findOrFail($id);
            $role->permisos()->detach();
            $role->delete();

            return response()->json([
                'message' => 'Rol eliminado exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar el rol'
            ], 500);
        }
    }
}
