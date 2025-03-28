<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Filament\Models\Contracts\HasAvatar;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser, HasAvatar
{
    use HasFactory, Notifiable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name', 'username', 'birthday','email','email_empresa','phone','phone_empresa', 'password',
        'avatar', 'rfc', 'rfc_doc', 'curp', 'curp_doc', 'imss', 'imss_doc', 'comprobante_domicilio_doc',
        'banco', 'tarjeta', 'cuenta', 'clabe', 'fecha_inicio', 'fecha_fin',
        'licencia_image', 'ine_image', 'color', 'role', 'is_active'
    ];

    protected $casts = [
        'ine_image' => 'array',
         'licencia_image' => 'array'
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

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'dashboard') {
            return str_ends_with($this->role, 'Administrador') || str_ends_with($this->role, 'Gerente') || str_ends_with($this->role, 'Oficina');
        }
 
        return true;
    }

    public function getFilamentAvatarUrl(): ?string
    {
        if(!$this->avatar || $this->avatar == null)
        {
            return "https://app.osberthvalle.com/storage/user-avatar/default.png";
        }
        return "https://app.osberthvalle.com/storage/".$this->avatar;
    }


    /************************** Relationships **************************/


    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function zonas(): HasMany
    {
        return $this->hasMany(Zonas::class);
    }

    public function rutas(): BelongsTo
    {
        return $this->belongsTo(Rutas::class, 'zonas_id');
    }
}
