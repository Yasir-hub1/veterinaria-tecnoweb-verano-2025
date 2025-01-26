<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $tableName = "productos";
    protected $fillable = ['categoria_id',"nombre","stock","precio","imagen","descripcion"];
    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }
}
