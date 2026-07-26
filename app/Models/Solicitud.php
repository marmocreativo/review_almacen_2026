<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Solicitud extends Model
{
    protected $table = 'al_solicitudes';
    protected $primaryKey = 'ID_SOLICITUD';

    protected $fillable = [
        'ID_EMPRESA',
        'ID_SEDE',
        'ID_CONTACTO',
        'RESPONSABLE_TITULO',
        'RESPONSABLE_NOMBRE',
        'RESPONSABLE_CORREO',
        'RESPONSABLE_TELEFONO',
        'RESPONSABLE_CELULAR',
        'DIRECCION_ENVIO',
        'SESIONES_SIMULTANEAS',
        'CANTIDAD_SIMULTANEAS',
        'HORARIO_DE_ATENCION',
        'OBSERVACIONES',
        'CANTIDAD_EXAMENES',
        'CANTIDAD_EXAMENES_APLICADOS',
        'ESTADO_SOLICITUD',
        'APROBACION',
        'ESTADO_FACTURA',
        'FECHA_SOLICITUD',
        'IMPORTE_FACTURA',
        'FOLIO_FACTURA',
        'FACTURA_PDF',
        'FACTURA_XML',
        'FECHA_VENCIMIENTO_COBRANZA',
        'ENVIO_ZONA',
        'ENVIO_EXAMEN',
        'ENVIO_VERSION',
        'ENVIO_NUMERO_HOJAS',
        'FECHA_PRIMERA_APLICACION',
        'CANTIDAD_USB',
        'CANTIDAD_CD',
        'ENVIO_PASS_USB',
        'ENVIO_CANTIDAD_SOBRES',
        'ENVIO_FOLIOS_AUDIO',
        'ENVIO_FECHA_ENVIO',
        'ENVIO_DIAS_PERMITIDO',
        'ENVIO_PAQUETERIA',
        'ENVIO_PAQUETERIA_GUIA',
        'ENVIO_PAQUETERIA_COSTO',
        'ENVIO_NOTAS',
        'FACTURACION_FECHA',
        'FACTURACION_DIAS_CREDITO',
        'FACTURACION_NOTAS',
    ];

    protected $casts = [
        'FECHA_SOLICITUD' => 'datetime',
        'FECHA_PRIMERA_APLICACION' => 'date',
        'IMPORTE_FACTURA' => 'decimal:2',
        'FECHA_VENCIMIENTO_COBRANZA' => 'date',
        'ENVIO_FECHA_ENVIO'        => 'date',
        'ENVIO_PAQUETERIA_COSTO'   => 'decimal:2',
        'FACTURACION_FECHA'        => 'date',
    ];

    public $timestamps = false;

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class, 'ID_EMPRESA');
    }

    public function sede(): BelongsTo
    {
        return $this->belongsTo(Sede::class, 'ID_SEDE');
    }

    public function contacto(): BelongsTo
    {
        return $this->belongsTo(Contacto::class, 'ID_CONTACTO');
    }

    public function examenes(): HasMany
    {
        return $this->hasMany(SolicitudExamen::class, 'ID_SOLICITUD', 'ID_SOLICITUD');
    }

    public function articulos(): HasMany
    {
        return $this->hasMany(SolicitudArticulo::class, 'ID_SOLICITUD', 'ID_SOLICITUD');
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(SolicitudPago::class, 'ID_SOLICITUD', 'ID_SOLICITUD')->orderBy('FECHA_PAGO', 'desc');
    }

    public function totalPagado(): float
    {
        return (float) $this->pagos()->sum('IMPORTE');
    }

    public function saldoPendiente(): float
    {
        return max(0, (float) $this->IMPORTE_FACTURA - $this->totalPagado());
    }

    /**
     * Estatus de cobranza: pagado > vencido > pendiente.
     * Solo aplica si la solicitud ya está facturada.
     */
    public function estadoCobranza(): array
    {
        if ($this->ESTADO_FACTURA !== 'facturada') {
            return ['label' => 'No aplica', 'class' => 'badge-ghost'];
        }

        $pagado = $this->totalPagado();

        if ($this->IMPORTE_FACTURA > 0 && $pagado >= $this->IMPORTE_FACTURA) {
            return ['label' => 'Pagado', 'class' => 'badge-success'];
        }

        if ($this->FECHA_VENCIMIENTO_COBRANZA && now()->startOfDay()->gt($this->FECHA_VENCIMIENTO_COBRANZA)) {
            return ['label' => 'Vencido', 'class' => 'badge-error'];
        }

        return ['label' => 'Pendiente', 'class' => 'badge-warning'];
    }

    public function isPendiente(): bool
    {
        return $this->ESTADO_SOLICITUD === 'pendiente';
    }

    public function isEnviada(): bool
    {
        return $this->ESTADO_SOLICITUD === 'enviada';
    }

    public function isRetornada(): bool
    {
        return $this->ESTADO_SOLICITUD === 'retornada';
    }
    
    public function getRouteKeyName(): string
    {
        return 'ID_SOLICITUD';
    }
}