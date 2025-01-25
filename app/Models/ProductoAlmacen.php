<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductoAlmacen extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $tableName = "productos_almacen";
    protected $fillable = ['producto_id',"almacen_id","stock"];

}
