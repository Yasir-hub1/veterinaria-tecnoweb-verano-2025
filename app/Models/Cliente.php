<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = "clientes";
    protected $fillable = ["id",'tipo',"nombre","apellido","razon_social","nit","direccion","celular","email","genero"];


    public function mascotas()
    {
        return $this->hasMany(Mascota::class);
    }

    public function ventas()
    {
        return $this->hasMany(NotaVenta::class);
    }
}
