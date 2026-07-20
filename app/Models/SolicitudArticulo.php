<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SolicitudArticulo extends Model
{
    protected $table = 'al_solicitudes_articulos';
    protected $primaryKey = 'ID';

    protected $fillable = [
        'ID_SOLICITUD',
        'ID_ARTICULO',
        'ID_EXAMEN',
        'FOLIO',
        'SERIE',
        'SERIE_NUMERICO',
        'FORMATO',
        'NOMBRE',
        'CANTIDAD_ENVIADA',
        'CANTIDAD_A_ALMACEN',
        'CANTIDAD_A_DESTRUCCION',
        'CANTIDAD_PERDIDOS',
        'CANTIDAD_COBRAR',
        'UBICACION_DESTRUCCION',
        'RAZON_PERDIDA',
        'FECHA_RETORNO',
        'PRECIO_VENTA',
        'NOMBRE_CANDIDATO',
        'ESTADO',
    ];

    protected $casts = [
        'FECHA_RETORNO' => 'date',
    ];

    public $timestamps = false;

    public function solicitud(): BelongsTo
    {
        return $this->belongsTo(Solicitud::class, 'ID_SOLICITUD', 'ID_SOLICITUD');
    }

    public function articulo(): BelongsTo
    {
        return $this->belongsTo(Articulo::class, 'ID_ARTICULO', 'ID_ARTICULO');
    }

    public function examen(): BelongsTo
    {
        return $this->belongsTo(SolicitudExamen::class, 'ID_EXAMEN', 'ID');
    }

    public function tieneSerie(): bool
    {
        return !empty($this->SERIE);
    }

    public function isRetornado(): bool
    {
        return $this->ESTADO === 'retornado';
    }
}