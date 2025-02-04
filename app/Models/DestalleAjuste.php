<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DestalleAjuste extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = "detalles_ajuste";
    protected $fillable = ['ajuste_id',"producto_id","cantidad"];
    protected $primaryKey = ['ajuste_id', 'producto_id'];
    public $incrementing = false;

    public function ajuste()
    {
        return $this->belongsTo(AjusteInventario::class, 'ajuste_id');
    }

    // Relationship to ProductoAlmacen
    public function productoAlmacen()
    {
        return $this->belongsTo(ProductoAlmacen::class, 'producto_id', 'id');
    }
}
