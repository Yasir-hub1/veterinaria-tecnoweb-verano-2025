<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Traits\HasRolesAndPermissions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;

class Usuario extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable,HasRolesAndPermissions;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    public $timestamps = false;

    protected $table = "usuarios";

    protected $fillable = [
        'name',
        'email',
        "cedula",
        "celular",
        "genero",
        'password',
        'tipo_id',
        "estado"

    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'usuario_rol_permiso', 'usuario_id', 'rol_id')
                    ->withPivot('permiso_id');
    }

    public function permisos()
    {
        return $this->belongsToMany(Permiso::class, 'usuario_rol_permiso', 'usuario_id', 'permiso_id')
                    ->withPivot('rol_id');
    }

    public function tienePermiso($permissionName)
    {
        return $this->roles()
            ->whereHas('permisos', function($query) use ($permissionName) {
                $query->where('permisos.nombre', $permissionName);
            })
            ->exists();
    }

    public function tipo()
    {
        return $this->belongsTo(Tipo::class);
    }




    public function ordenes()
    {
        return $this->hasMany(OrdenServicio::class, 'usuario_id');
    }
    public function notaVenta()
    {
        return $this->hasMany(NotaVenta::class, 'usuario_id');
    }

    public function ajustesInventario()
    {
        return $this->hasMany(AjusteInventario::class, 'usuario_id');
    }

    // En el modelo Usuario
    public function sincronizarRolesYPermisos($roles)
    {
        DB::table('usuario_rol_permiso')->where('usuario_id', $this->id)->delete();

        foreach ($roles as $rolId) {
            $permisosRol = DB::table('roles_permisos')
                            ->where('role_id', $rolId)
                            ->get();

            foreach ($permisosRol as $permisoRol) {
                DB::table('usuario_rol_permiso')->insert([
                    'usuario_id' => $this->id,
                    'rol_id' => $rolId,
                    'permiso_id' => $permisoRol->permiso_id
                ]);
            }
        }
    }

// public function hasPermission($permission)
// {
//     return $this->roles()
//         ->whereHas('permisos', function($query) use ($permission) {
//             $query->where('nombre', $permission);
//         })->exists();
// }

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


// public function hasAnyPermission($permissions)
// {
//     return $this->roles()
//         ->whereHas('permisos', function($query) use ($permissions) {
//             $query->whereIn('nombre', (array) $permissions);
//         })->exists();
// }

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


public function hasRole($role)
{
    return $this->roles->contains('nombre', $role);
}

public function hasAnyRole($roles)
{
    return $this->roles()->whereIn('nombre', (array) $roles)->exists();
}
}
