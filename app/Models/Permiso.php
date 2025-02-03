<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permiso extends Model
{
    public $timestamps = false;
    protected $table = "permisos";
    protected $fillable = ["id",'nombre'];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'roles_permisos', 'permiso_id', 'role_id');
    }

    public function usuarios()
    {
        return $this->belongsToMany(User::class, 'usuario_rol_permiso', 'permiso_id', 'usuario_id')
                    ->withPivot('rol_id');
    }
}
