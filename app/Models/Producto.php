<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = "productos";
    protected $fillable = ['categoria_id',"nombre","precio","imagen","descripcion"];
    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }

    public function almacenes()
    {
        return $this->belongsToMany(Almacen::class, 'productos_almacen', 'producto_id', 'almacen_id')
                    ->withPivot('stock'); // Incluye el campo extra "stock"
    }
}
