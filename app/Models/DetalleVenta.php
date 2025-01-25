<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleVenta extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $tableName = "detalles_venta";
    protected $fillable = ['nota_venta_id',"producto_id","cantidad","total"];
}
