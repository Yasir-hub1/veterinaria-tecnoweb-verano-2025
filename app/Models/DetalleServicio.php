<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleServicio extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = "detalles_servicio";
    protected $fillable = ['orden_servicio_id',"servicio_id"];
}
