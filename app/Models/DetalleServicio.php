<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleServicio extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $tableName = "detalles_servicio";
    protected $fillable = ['nota_servicio_id',"servicio_id","cantidad","total"];
}
