<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RolesPermiso extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $tableName = "roles_permisos";
    protected $fillable = ['role_id',"permiso_id","usuario_id"];

}
