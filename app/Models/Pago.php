<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $tableName = "pagos";
    protected $fillable = ['nota_servicio_id',"servicio_id","cantidad","total"];
}
