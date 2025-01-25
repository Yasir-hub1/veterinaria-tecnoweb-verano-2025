<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $tableName = "clientes";
    protected $fillable = ['tipo',"nombre","apellido","razon_social","nit","direccion","celular","email","genero"];

}
