<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrdenCompra extends Model
{
    protected $table = 'al_ordenes_compra';
    protected $primaryKey = 'ID';

    protected $fillable = [
        'ID_ORDEN',
        'FECHA_REGISTRO',
        'FOLIO_FACTURA',
        'IMPORTE_FACTURA',
        'FACTURA_PDF',
        'FACTURA_XML',
    ];

    protected $casts = [
        'FECHA_REGISTRO'  => 'datetime',
        'IMPORTE_FACTURA' => 'decimal:2',
    ];

    public $timestamps = false;

    public function articulos(): HasMany
    {
        return $this->hasMany(OrdenArticulo::class, 'ID_ORDEN', 'ID_ORDEN');
    }
}