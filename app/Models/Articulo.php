<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Articulo extends Model
{
    protected $table = 'al_articulos';
    protected $primaryKey = 'ID_ARTICULO';

    protected $fillable = [
        'FOLIO',
        'SERIE',
        'SERIE_NUMERICO',
        'NOMBRE',
        'DESCRIPCION',
        'FORMATO',
        'COSTO_UNITARIO',
        'PRECIO_VENTA',
        'CANTIDAD_ALMACEN',
        'CANTIDAD_SOLICITUDES',
        'CANTIDAD_DESTRUCCION',
        'CANTIDAD_PERDIDOS',
        'UBICACION_UNICA',
        'TIPO',
    ];

    protected $casts = [
        'COSTO_UNITARIO' => 'decimal:2',
        'PRECIO_VENTA'   => 'decimal:2',
        'FECHA_ACTUALIZACION' => 'datetime',
    ];

    public $timestamps = false;

    public function tieneSerie(): bool
    {
        return !empty($this->SERIE);
    }

    public function estaEnAlmacen(): bool
    {
        return $this->CANTIDAD_ALMACEN > 0;
    }

    public function scopePorFolio($query, $folio)
    {
        return $query->where('FOLIO', $folio);
    }

    public function scopePorSerie($query, $serie)
    {
        return $query->where('SERIE', $serie);
    }
    public function getRouteKeyName(): string
    {
        return 'ID_ARTICULO';
    }

    public function solicitudesArticulos()
    {
        return $this->hasMany(SolicitudArticulo::class, 'ID_ARTICULO', 'ID_ARTICULO');
    }

    public function ordenesArticulos()
    {
        return $this->hasMany(OrdenArticulo::class, 'ID_ARTICULO', 'ID_ARTICULO');
    }

    public function esDeletable(): bool
    {
        return $this->solicitudesArticulos()->doesntExist()
            && $this->ordenesArticulos()->doesntExist()
            && $this->CANTIDAD_DESTRUCCION == 0;
    }

    /**
     * Determina el badge de estado dominante según prioridad de negocio:
     * Perdidos > Destrucción > Solicitudes > Almacén > Sin existencias.
     */
    public static function estadoBadge($almacen, $solicitudes, $destruccion, $perdidos): array
    {
        if ($perdidos > 0) {
            return ['label' => 'Con pérdidas', 'class' => 'badge-error'];
        }
        if ($destruccion > 0) {
            return ['label' => 'En destrucción', 'class' => 'badge-warning'];
        }
        if ($solicitudes > 0) {
            return ['label' => 'En solicitudes', 'class' => 'badge-info'];
        }
        if ($almacen > 0) {
            return ['label' => 'En almacén', 'class' => 'badge-success'];
        }
        return ['label' => 'Sin existencias', 'class' => 'badge-ghost'];
    }
}