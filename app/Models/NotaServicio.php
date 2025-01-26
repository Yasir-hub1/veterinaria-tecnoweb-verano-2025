<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotaServicio extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = "notas_servicio";
    protected $fillable = ['mascota_id',"usuario_id","fecha","montototal","metodopago","estado"];

}
