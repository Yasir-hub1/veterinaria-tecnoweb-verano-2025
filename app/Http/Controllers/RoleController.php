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
            'ver_usuario',
            'guardar_usuario',
            'editar_usuario',
            'eliminar_usuario',

            'ver_rol',
            'guardar_rol',
            'editar_rol',
            'eliminar_rol',

            "ver_asignacion",
            "asignar_rol"

        ],
        'Gestión de Mascotas' => [

            'ver_mascota',
            'guardar_mascota',
            'editar_mascota',
            'eliminar_mascota',

            'ver_cliente',
            'guardar_cliente',
            'editar_cliente',
            'eliminar_cliente',

            'ver_servicio',
            'guardar_servicio',
            'editar_servicio',
            'eliminar_servicio',


        ],
        'Gestión de Inventario' => [

            'ver_producto',
            'guardar_producto',
            'editar_producto',
            'eliminar_producto',

             'ver_registro_inventario',
            'guardar_registro_inventario',


            'ver_ajuste_inventario',
            'guardar_ajuste_inventario',


            'ver_almacen',
            'guardar_almacen',
            'editar_almacen',
            'eliminar_almacen',
        ],

        'Gestión de Ventas' => [
            // aquí los permisos de ventas
            'ver_venta',
            'guardar_venta',
            // 'eliminar_venta',

            'ver_pago',

            'ver_orden_servicio',
            'guardar_orden_servicio',

            // 'eliminar_orden_servicio'
        ],
        'Gestión de Reportes y estadisticas' => [
            // aquí los permisos de reportes
            'ver_reporte_venta',
            'ver_reporte_orden_servicio',

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

    // public function update(Request $request, $id)
    // {
    //     $request->validate([
    //         'nombre' => 'required|string|max:255|unique:roles,nombre,'.$id,
    //         'permisos' => 'required|array'
    //     ]);

    //     try {
    //         DB::beginTransaction();

    //         $role = Role::findOrFail($id);
    //         $role->update([
    //             'nombre' => $request->nombre
    //         ]);

    //         $role->permisos()->sync($request->permisos);

    //         DB::commit();

    //         return response()->json([
    //             'message' => 'Rol actualizado exitosamente',
    //             'role' => $role
    //         ]);

    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return response()->json([
    //             'message' => 'Error al actualizar el rol'
    //         ], 500);
    //     }
    // }

    public function update(Request $request, $id)
{
    $request->validate([
        'nombre' => 'required|string|max:255|unique:roles,nombre,'.$id,
        'permisos' => 'required|array'
    ]);

    try {
        DB::beginTransaction();

        // 1. Actualizar el rol
        $role = Role::findOrFail($id);
        $role->update([
            'nombre' => $request->nombre
        ]);

        // 2. Sincronizar los permisos en la tabla roles_permisos
        $role->permisos()->sync($request->permisos);

        // 3. Actualizar la tabla usuario_rol_permiso
        // Primero, obtener todos los usuarios que tienen este rol
        $usuariosConRol = DB::table('usuario_rol_permiso')
            ->where('rol_id', $id)
            ->select('usuario_id')
            ->distinct()
            ->get();

        // Eliminar los registros antiguos para este rol
        DB::table('usuario_rol_permiso')
            ->where('rol_id', $id)
            ->delete();

        // Insertar los nuevos registros para cada usuario que tenía el rol
        foreach ($usuariosConRol as $usuario) {
            foreach ($request->permisos as $permiso_id) {
                DB::table('usuario_rol_permiso')->insert([
                    'usuario_id' => $usuario->usuario_id,
                    'rol_id' => $id,
                    'permiso_id' => $permiso_id
                ]);
            }
        }

        DB::commit();

        return response()->json([
            'message' => 'Rol y permisos actualizados exitosamente',
            'role' => $role
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'message' => 'Error al actualizar el rol y permisos: ' . $e->getMessage()
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
