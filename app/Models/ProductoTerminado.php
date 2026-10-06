<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductoTerminado extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'codigo_producto',
        'concentracion',
        'presentacion',
        'forma_farmaceutica',
        'precio_venta',
        'estado',
    ];
}
