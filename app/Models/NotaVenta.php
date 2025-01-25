<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotaVenta extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = "notas_venta";
    protected $fillable = ['cliente_id',"usuario_id","fecha","metodopago","montototal","estado"];
}
