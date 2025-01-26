<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Almacen extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = "almacenes";
    protected $fillable = ["nombre","descripcion"];

    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'productos_almacen', 'almacen_id', 'producto_id')
                    ->withPivot('stock'); // Incluye el campo extra "stock"
    }
}
