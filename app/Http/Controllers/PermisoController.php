<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class PermisoController extends Controller
{
    public function edit(User $usuario)
    {
        $modulos = $this->getModulosConPermisos();
        return view('users.permissions', compact('usuario', 'modulos'));
    }

    public function update(Request $request, User $usuario)
    {
        try {
            DB::beginTransaction();

            // Obtener los permisos actuales del usuario
            $permisosActuales = $usuario->permisos->pluck('id')->toArray();

            // Obtener los nuevos permisos seleccionados
            $nuevosPermisos = $request->input('permissions', []);

            // Actualizar los permisos del usuario
            $usuario->permisos()->sync($nuevosPermisos);

            // Registrar el cambio en el log
            activity()
                ->performedOn($usuario)
                ->causedBy(auth()->user())
                ->withProperties([
                    'permisos_anteriores' => $permisosActuales,
                    'permisos_nuevos' => $nuevosPermisos
                ])
                ->log('actualización de permisos');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Permisos actualizados correctamente'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar los permisos',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function getModulosConPermisos()
    {
        return [
            'usuarios' => [
                'nombre' => 'Usuarios',
                'icono' => 'fas fa-users',
                'permisos' => [
                    'ver_usuarios' => 'Ver Usuarios',
                    'crear_usuarios' => 'Crear Usuarios',
                    'editar_usuarios' => 'Editar Usuarios',
                    'eliminar_usuarios' => 'Eliminar Usuarios'
                ]
            ],
            'mascotas' => [
                'nombre' => 'Mascotas',
                'icono' => 'fas fa-paw',
                'permisos' => [
                    'ver_mascotas' => 'Ver Mascotas',
                    'crear_mascotas' => 'Crear Mascotas',
                    'editar_mascotas' => 'Editar Mascotas',
                    'eliminar_mascotas' => 'Eliminar Mascotas'
                ]
            ],
            'servicios' => [
                'nombre' => 'Servicios',
                'icono' => 'fas fa-stethoscope',
                'permisos' => [
                    'ver_servicios' => 'Ver Servicios',
                    'crear_servicios' => 'Crear Servicios',
                    'editar_servicios' => 'Editar Servicios',
                    'eliminar_servicios' => 'Eliminar Servicios'
                ]
            ],
            // Agregar más módulos según sea necesario
        ];
    }
}
