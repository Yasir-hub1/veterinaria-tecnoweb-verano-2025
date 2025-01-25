<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    public $timestamps = false;
    protected $table = 'roles';

    protected $fillable = ["id",'nombre'];

    public function permisos()
    {
        return $this->belongsToMany(Permiso::class, 'roles_permisos', 'role_id', 'permiso_id');
    }

    public function usuarios()
    {
        return $this->belongsToMany(Usuario::class, 'roles_permisos')
                    ->withPivot('permiso_id');
    }
}
