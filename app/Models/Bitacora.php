<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bitacora extends Model
{
    const UPDATED_AT = null;

    protected $fillable = [
        'id_user',
        'modulo',
        'accion',
        'descripcion',
        'id_registro',
        'datos',
        'ip',
    ];

    protected $casts = [
        'datos' => 'array',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    /**
     * Atajo para registrar una entrada desde cualquier controlador.
     * Ej: Bitacora::registrar('articulos', 'eliminar', "Eliminó S000000123", $articulo->ID_ARTICULO, ['folio' => ..., 'serie' => ...]);
     */
    public static function registrar(string $modulo, string $accion, string $descripcion, ?int $idRegistro = null, array $datos = []): self
    {
        return self::create([
            'id_user'     => auth()->id(),
            'modulo'      => $modulo,
            'accion'      => $accion,
            'descripcion' => $descripcion,
            'id_registro' => $idRegistro,
            'datos'       => $datos ?: null,
            'ip'          => request()->ip(),
        ]);
    }
}