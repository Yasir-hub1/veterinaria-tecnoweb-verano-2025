<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotaCompra extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $tableName = "notas_compras";
    protected $fillable = ['usuario_id',"proveedor_id","fecha","glosa"];
}
