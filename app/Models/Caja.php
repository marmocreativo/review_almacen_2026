<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Caja extends Model
{
    protected $table = 'al_cajas';
    protected $primaryKey = 'ID';

    protected $fillable = [
        'NOMBRE',
        'NUMERO',
        'FECHA_CIERRE',
        'FECHA_DESTRUIDA',
    ];

    protected $casts = [
        'FECHA_CREACION'  => 'datetime',
        'FECHA_CIERRE'    => 'datetime',
        'FECHA_DESTRUIDA' => 'datetime',
    ];

    public $timestamps = false;

    public function articulos(): HasMany
    {
        return $this->hasMany(SolicitudArticulo::class, 'ID_CAJA', 'ID');
    }

    public function estaCerrada(): bool
    {
        return !is_null($this->FECHA_CIERRE);
    }

    public function estaDestruida(): bool
    {
        return !is_null($this->FECHA_DESTRUIDA);
    }
    
    public function getRouteKeyName(): string
    {
        return 'ID';
    }
}