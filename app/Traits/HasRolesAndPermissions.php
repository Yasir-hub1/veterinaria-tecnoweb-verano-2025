<?php

namespace App\Traits;

trait HasRolesAndPermissions
{
    public function hasPermission($permission)
    {
        return $this->roles()
            ->whereHas('permisos', function($query) use ($permission) {
                $query->where('nombre', $permission);
            })->exists();
    }

    public function hasAnyPermission($permissions)
    {
        return $this->roles()
            ->whereHas('permisos', function($query) use ($permissions) {
                $query->whereIn('nombre', (array) $permissions);
            })->exists();
    }

    public function hasRole($role)
    {
        return $this->roles->contains('nombre', $role);
    }

    public function hasAnyRole($roles)
    {
        return $this->roles()->whereIn('nombre', (array) $roles)->exists();
    }
}
