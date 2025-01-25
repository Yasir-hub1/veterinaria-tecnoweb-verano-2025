<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotaVenta extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $tableName = "notas_venta";
    protected $fillable = ['nota_servicio_id',"servicio_id","cantidad","total"];
}
