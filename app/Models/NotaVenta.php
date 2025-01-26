<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotaVenta extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = "notas_venta";
    protected $fillable = ['cliente_id',"usuario_id","fecha","tipopago","total","estado"];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }

    // Relación: una venta tiene muchos productos a través de detalles de ventas
    public function productos()
    {
        return $this->belongsToMany(ProductoAlmacen::class, 'detalles_ventas')
                    ->withPivot('cantidad', 'precio'); // columnas adicionales si las tienes
    }
}
