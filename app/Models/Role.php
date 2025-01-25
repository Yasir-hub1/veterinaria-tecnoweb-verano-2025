<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    public $timestamps = false;
    protected $fillable = ["id",'nombre'];

    public function permisos()
    {
        return $this->belongsToMany(Permiso::class, 'roles_permisos');
    }

    public function usuarios()
    {
        return $this->hasMany(usuario::class);
    }
}
