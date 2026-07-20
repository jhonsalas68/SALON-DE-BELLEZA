<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nombre',
        'apellido',
        'name',
        'email',
        'telefono',
        'password',
        'role_id',
        'comision_porcentaje',
        'puntos',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

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

    /**
     * Relationship with Role.
     */
    public function role(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Check if user has a specific role by slug.
     */
    public function hasRole(string $roleSlug): bool
    {
        return $this->role && $this->role->slug === $roleSlug;
    }

    /**
     * Check if user has a specific permission by slug.
     */
    public function hasPermission(string $permissionSlug): bool
    {
        if ($this->hasRole('administrador')) {
            return true;
        }
        return $this->role && $this->role->hasPermission($permissionSlug);
    }

    /**
     * Relationship with activity logs.
     */
    public function activityLogs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    /**
     * Relationship with comisiones.
     */
    public function comisiones(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Comision::class, 'estilista_id');
    }

    public function citas(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Cita::class, 'estilista_id');
    }

    public function puntosHistorial(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PuntosHistorial::class);
    }

    public function valoracionesRecibidas(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Valoracion::class, 'estilista_id');
    }
}
