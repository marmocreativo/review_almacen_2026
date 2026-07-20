<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Empresa extends Model
{
    protected $fillable = [
        'nombre',
        'razon_social',
        'rfc',
        'logo',
        'estado',
    ];

    public function sedes(): HasMany
    {
        return $this->hasMany(Sede::class, 'id_empresa');
    }
}