<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UsuarioRolPermisoController extends Controller
{
    public function index()
    {
        $usuarios = Usuario::with(['roles' => function($query) {
            $query->distinct();
        }])->get();
        $roles = Role::with('permisos')->get();
        return view('asignacion-roles.index', compact('usuarios', 'roles'));
    }

    public function show($id)
    {
        $usuario = Usuario::with('roles')->findOrFail($id);
        return response()->json($usuario);
    }
    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            // \Log::info('Request data:', $request->all());
            // \Log::info('User ID:', ['id' => $id]);

            $usuario = Usuario::findOrFail($id);

            // Log the roles being synced
            // \Log::info('Roles to sync:', ['roles' => $request->input('roles')]);

            // Get the roles data
            $roles = $request->input('roles');

            if (empty($roles)) {
                throw new \Exception('No roles selected');
            }

            // Delete existing assignments
            DB::table('usuario_rol_permiso')->where('usuario_id', $id)->delete();

            // Get permissions for each role
            foreach ($roles as $rolId) {
                $permisosRol = DB::table('roles_permisos')
                                ->where('role_id', $rolId)
                                ->get();

                // \Log::info('Permissions for role ' . $rolId . ':', ['permisos' => $permisosRol]);

                if ($permisosRol->isEmpty()) {
                    return response()->json([
                        'message' => 'Error al asignar roles y permisos, el rol  no tiene permisos',

                    ], 500);

                }

                // Create new assignments
                foreach ($permisosRol as $permisoRol) {
                    DB::table('usuario_rol_permiso')->insert([
                        'usuario_id' => $id,
                        'rol_id' => $rolId,
                        'permiso_id' => $permisoRol->permiso_id
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Roles y permisos asignados exitosamente'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error in role assignment: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'message' => 'Error al asignar roles y permisos',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
