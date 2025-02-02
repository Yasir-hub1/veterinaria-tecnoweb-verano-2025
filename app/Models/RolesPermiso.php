<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RolesPermiso extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = "roles_permisos";
    protected $fillable = ['role_id',"permiso_id"];

    public function rol()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function permiso()
    {
        return $this->belongsTo(Permiso::class, 'permiso_id');
    }

    public function usuariosRoles()
    {
        return $this->hasMany(UsuarioRolPermiso::class, 'rol_id', 'role_id');
    }

}
