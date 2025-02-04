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
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function detalles()
    {
        return $this->hasMany(DestalleAjuste::class, 'ajuste_id');
    }

    // Through detalles, we can access ProductoAlmacen
    public function productosAlmacen()
    {
        return $this->hasManyThrough(
            ProductoAlmacen::class,    // Modelo objetivo
            DestalleAjuste::class,      // Modelo intermedio
            'ajuste_id',               // Clave foránea en DetalleAjuste
            'id',                      // Clave foránea en ProductoAlmacen (ajustada correctamente)
            'id',                      // Clave local en AjusteInventarios
            'producto_id'              // Clave local en DetalleAjuste
        );
    }

}
