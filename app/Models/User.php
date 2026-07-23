<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['name', 'email', 'password', 'pin', 'pin_set_at'])]
#[Hidden(['password', 'remember_token', 'pin'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function userRole(): HasOne
    {
        return $this->hasOne(UserRole::class, 'id_user', 'id');
    }

    public function role(): ?Role
    {
        return $this->userRole?->role;
    }

    public function tieneAcceso(string $seccion): bool
    {
        return $this->role()?->tieneAcceso($seccion) ?? false;
    }

    public function tienePin(): bool
    {
        return !empty($this->pin);
    }

    public function verificarPin(string $pin): bool
    {
        return $this->pin && \Illuminate\Support\Facades\Hash::check($pin, $this->pin);
    }
}
