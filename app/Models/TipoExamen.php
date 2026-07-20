<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoExamen extends Model
{
    protected $table = 'tipo_examenes';

    protected $fillable = [
        'nombre',
        'descripcion',
        'estado',
        'candidatos_minimos',
        'dias_anticipacion',
    ];
}