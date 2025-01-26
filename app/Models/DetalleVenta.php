<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleVenta extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = "detalles_venta";
    protected $fillable = ['nota_venta_id',"producto_id","cantidad","total"];

    public function venta()
    {
        return $this->belongsTo(NotaVenta::class);
    }

    // Definir la relación con el producto almacen
    public function productoAlmacen()
    {
        return $this->belongsTo(ProductoAlmacen::class, 'producto_almacen_id');
    }
}
