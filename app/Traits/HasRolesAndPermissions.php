<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;

trait HasRolesAndPermissions
{
    public function hasPermission($permission)
    {
        return DB::table('usuarios')
            ->join('usuario_rol_permiso', 'usuarios.id', '=', 'usuario_rol_permiso.usuario_id')
            ->join('roles_permisos', function($join) {
                $join->on('usuario_rol_permiso.rol_id', '=', 'roles_permisos.role_id')
                     ->on('usuario_rol_permiso.permiso_id', '=', 'roles_permisos.permiso_id');
            })
            ->join('permisos', 'roles_permisos.permiso_id', '=', 'permisos.id')
            ->where('usuarios.id', $this->id)
            ->where('permisos.nombre', $permission)
            ->exists();
    }

    public function hasAnyPermission($permissions)
    {
        return DB::table('usuarios')
            ->join('usuario_rol_permiso', 'usuarios.id', '=', 'usuario_rol_permiso.usuario_id')
            ->join('roles_permisos', function($join) {
                $join->on('usuario_rol_permiso.rol_id', '=', 'roles_permisos.role_id')
                     ->on('usuario_rol_permiso.permiso_id', '=', 'roles_permisos.permiso_id');
            })
            ->join('permisos', 'roles_permisos.permiso_id', '=', 'permisos.id')
            ->where('usuarios.id', $this->id)
            ->whereIn('permisos.nombre', (array) $permissions)
            ->exists();
    }
}
