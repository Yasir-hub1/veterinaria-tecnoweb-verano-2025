<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AjusteInventario extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = "ajustes_inventario";
    protected $fillable = ['usuario_id',"tipo","fecha","glosa"];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function detalles()
    {
        return $this->hasMany(DestalleAjuste::class, 'ajuste_id');
    }

    // Through detalles, we can access ProductoAlmacen
    public function productosAlmacen()
    {
        return $this->hasManyThrough(
            ProductoAlmacen::class,    // Target model
            DestalleAjuste::class,      // Intermediate model
            'ajuste_id',               // Foreign key on detalles_ajuste
            'producto_id',             // Foreign key on productos_almacen
            'id',                      // Local key on ajustes_inventario
            'producto_id'              // Local key on detalles_ajuste
        );
    }

}
