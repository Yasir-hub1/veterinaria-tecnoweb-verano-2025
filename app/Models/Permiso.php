<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permiso extends Model
{
    public $timestamps = false;
    protected $tableName = "permisos";
    protected $fillable = ["id",'nombre'];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'roles_permisos');
    }
}
