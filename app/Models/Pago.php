<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = "pagos";
    protected $fillable = ["orden_servicio_id",'nota_venta_id',"fechapago","estado","","tipopago"];

    public function notaVenta()
    {
        return $this->belongsTo(NotaVenta::class);
    }

    public function ordenServicio()
    {
        return $this->belongsTo(OrdenServicio::class);
    }
}
