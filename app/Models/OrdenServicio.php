<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OrdenServicio extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = "orden_servicio";
    protected $fillable = ['mascota_id',"usuario_id","fecha","total","estado"];

    // Relación: Una orden pertenece a una mascota
    public function mascota()
    {
        return $this->belongsTo(Mascota::class, 'mascota_id');
    }

    // Relación: Una orden pertenece a un usuario
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function pago(): HasOne
    {
        return $this->hasOne(Pago::class);
    }

}
