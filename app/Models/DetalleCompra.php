<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleCompra extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $tableName = "detalles_compra";
    protected $fillable = ['nota_compra_id',"productos_almacen_id","cantidad","precio"];

}
