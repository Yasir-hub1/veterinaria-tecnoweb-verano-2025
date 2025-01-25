<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Almacen extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $tableName = "almacenes";
    protected $fillable = ["nombre","descripcion"];
}
