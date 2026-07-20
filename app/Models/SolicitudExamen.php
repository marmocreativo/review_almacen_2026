<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SolicitudExamen extends Model
{
    protected $table = 'al_solicitudes_examenes';
    protected $primaryKey = 'ID';

    protected $fillable = [
        'ID_SOLICITUD',
        'EXAMEN',
        'CANTIDAD',
        'ESTADO',
        'FECHA',
    ];

    protected $casts = [
        'FECHA' => 'date',
    ];

    public $timestamps = false;

    public function solicitud(): BelongsTo
    {
        return $this->belongsTo(Solicitud::class, 'ID_SOLICITUD', 'ID_SOLICITUD');
    }

    public function articulos(): HasMany
    {
        return $this->hasMany(SolicitudArticulo::class, 'ID_EXAMEN', 'ID');
    }
}