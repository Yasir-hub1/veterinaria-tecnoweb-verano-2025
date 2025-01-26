<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Usuario extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    public $timestamps = false;
    protected $table = "usuarios";

    protected $fillable = [
        'name',
        'email',
        "cedula",
        "celular",
        "genero",
        'password',
        'tipo_id',

    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'roles_permisos', 'usuario_id', 'role_id');
    }

    public function tienePermiso($permissionName)
    {
        return $this->roles()
            ->whereHas('permisos', function($query) use ($permissionName) {
                $query->where('permisos.nombre', $permissionName);
            })
            ->exists();
    }

    public function tipo()
    {
        return $this->belongsTo(Tipo::class);
    }

    public function hasRole($roleName)
    {
        return $this->roles->contains('nombre', $roleName);
    }

    public function hasPermission($permissionName)
    {
        return $this->roles->flatMap->permisos->contains('nombre', $permissionName);
    }

    public function ordenes()
    {
        return $this->hasMany(OrdenServicio::class, 'usuario_id');
    }
}
