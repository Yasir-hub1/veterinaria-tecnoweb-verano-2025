<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mascota extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = "mascotas";
    protected $fillable = ['cliente_id',"nombre","edad","tipo","raza","imagen"];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function ordenes()
    {
        return $this->hasMany(OrdenServicio::class, 'mascota_id');
    }
}
