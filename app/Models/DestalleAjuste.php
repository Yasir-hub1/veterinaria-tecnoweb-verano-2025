<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DestalleAjuste extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = "detalles_ajuste";
    protected $fillable = ['ajuste_id',"producto_id","cantidad"];
}
