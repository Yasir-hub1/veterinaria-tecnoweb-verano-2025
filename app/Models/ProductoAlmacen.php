<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductoAlmacen extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = "productos_almacen";
    protected $fillable = ['producto_id',"almacen_id","stock"];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    // Relación con Almacen
    public function almacen()
    {
        return $this->belongsTo(Almacen::class, 'almacen_id');
    }

    public function detallesVentas()
    {
        return $this->hasMany(DetalleVenta::class);
    }
}
