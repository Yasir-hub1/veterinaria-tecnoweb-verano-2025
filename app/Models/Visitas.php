<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visitas extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = "visitas";
    protected $fillable = ['pagina',"conteo","fecha"];
}
