<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AjusteInventario extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = "ajustes_inventario";
    protected $fillable = ['usuario_id',"tipo","fecha","glosa"];

}
