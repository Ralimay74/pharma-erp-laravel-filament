<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Proveedor extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'nif_cif',
        'contacto',
        'email',
        'telefono',
        'direccion',
        'pais',
        'certificaciones',
        'estado',
        'notas',
    ];

    public function materiasPrimas(): HasMany
    {
        return $this->hasMany(MateriaPrima::class, 'proveedor_id');
    }
}
