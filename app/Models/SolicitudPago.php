<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SolicitudPago extends Model
{
    protected $table = 'al_solicitud_pagos';
    protected $primaryKey = 'ID_PAGO';

    protected $fillable = ['ID_SOLICITUD', 'FECHA_PAGO', 'IMPORTE', 'NOTAS'];

    protected $casts = [
        'FECHA_PAGO' => 'date',
        'IMPORTE'    => 'decimal:2',
    ];

    public function solicitud(): BelongsTo
    {
        return $this->belongsTo(Solicitud::class, 'ID_SOLICITUD', 'ID_SOLICITUD');
    }
}