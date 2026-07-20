<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $table = 'roles';

    protected $fillable = ['rol', 'permisos'];

    protected $casts = [
        'permisos' => 'array',
    ];

    public function usuarios(): HasMany
    {
        return $this->hasMany(UserRole::class, 'rol', 'id');
    }

    public function tieneAcceso(string $seccion): bool
    {
        return in_array($seccion, $this->permisos ?? []);
    }
}