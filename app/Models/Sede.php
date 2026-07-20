<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sede extends Model
{
    protected $fillable = [
        'id_empresa',
        'nombre',
        'calle_y_numero',
        'colonia_barrio',
        'alcaldia_municipio',
        'ciudad',
        'estado_republica',
        'codigo_postal',
        'referencias',
        'estado',
    ];

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class, 'id_empresa');
    }

    public function contactos(): HasMany
    {
        return $this->hasMany(Contacto::class, 'id_sede');
    }
}