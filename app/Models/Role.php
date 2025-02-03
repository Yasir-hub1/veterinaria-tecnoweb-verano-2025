<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    public $timestamps = false;
    protected $table = 'roles';

    protected $fillable = ["id",'nombre'];

    // Relación con permisos a través de rol_permiso
    // public function permisos()
    // {
    //     return $this->belongsToMany(Permiso::class, 'roles_permisos', 'role_id', 'permiso_id');
    // }

    // public function usuarios()
    // {
    //     return $this->belongsToMany(Usuario::class, 'usuario_rol_permiso', 'rol_id', 'usuario_id')
    //                 ->withPivot('permiso_id');
    // }

    // Relación con usuarios a través de usuario_rol_permiso que se conecta con rol_permiso

    // public function roles()
    // {
    //     return $this->belongsToMany(Role::class, 'rol_permiso', 'permiso_id', 'rol_id');
    // }


    public function permisos()
    {
        return $this->belongsToMany(Permiso::class, 'roles_permisos', 'role_id', 'permiso_id');
    }

    public function usuarios()
    {
        return $this->belongsToMany(Usuario::class, 'usuario_rol_permiso', 'rol_id', 'usuario_id')
                    ->withPivot('permiso_id');
    }
}
