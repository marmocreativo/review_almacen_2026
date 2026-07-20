<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserRole extends Model
{
    protected $table = 'user_roles';

    protected $fillable = ['id_user', 'rol'];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'rol', 'id');
    }
}