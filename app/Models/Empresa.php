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
        'tipo_cliente',
        'nombre_contacto_operativo',
        'apellido_contacto_operativo',
        'telefono_contacto_operativo',
        'email_contacto_operativo',
        'nombre_contacto_facturacion',
        'apellido_contacto_facturacion',
        'telefono_contacto_facturacion',
        'email_contacto_facturacion',
        'fecha_de_contrato',
        'vigencia_contrato',
        'dias_de_credito',
        'uso_de_cfdi',
        'direccion_fiscal',
        'portal_de_facturacion',
        'notas',
    ];

    protected $casts = [
        'fecha_de_contrato' => 'date',
        'vigencia_contrato' => 'integer',
        'dias_de_credito'   => 'integer',
    ];

    public function sedes(): HasMany
    {
        return $this->hasMany(Sede::class, 'id_empresa');
    }

    public function contactos(): HasMany
    {
        return $this->hasMany(Contacto::class, 'id_empresa');
    }
}