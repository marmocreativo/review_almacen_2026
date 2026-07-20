<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrdenArticulo extends Model
{
    protected $table = 'al_ordenes_articulos';
    protected $primaryKey = 'ID';

    protected $fillable = [
        'ID_ORDEN',
        'ID_ARTICULO',
        'FOLIO',
        'SERIE',
        'SERIE_NUMERICO',
        'FORMATO',
        'CANTIDAD',
        'COSTO_UNITARIO',
    ];

    public $timestamps = false;

    public function orden(): BelongsTo
    {
        return $this->belongsTo(OrdenCompra::class, 'ID_ORDEN', 'ID_ORDEN');
    }

    public function articulo(): BelongsTo
    {
        return $this->belongsTo(Articulo::class, 'ID_ARTICULO', 'ID_ARTICULO');
    }
}