<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Contacto extends Model
{
    protected $fillable = [
        'id_empresa',
        'nombre',
        'apellidos',
        'telefono',
        'correo',
        'pin',
    ];

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class, 'id_empresa');
    }

    public function sedes(): BelongsToMany
    {
        return $this->belongsToMany(Sede::class, 'contacto_sede', 'id_contacto', 'id_sede');
    }

    public static function generarPinUnico(): string
    {
        do {
            $pin = strtoupper(\Illuminate\Support\Str::random(8));
            $pin = preg_replace('/[^A-Z0-9]/', '', $pin);
            $pin = str_pad(substr($pin, 0, 8), 8, '7');
        } while (self::where('pin', $pin)->exists());

        return $pin;
    }
}