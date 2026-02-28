<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_global',
        'est_banni',
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
            'est_banni' => 'boolean',
        ];
    }

    public function adhesions()
    {
        return $this->hasMany(Adhesion::class, 'utilisateur_id');
    }

    public function hasActiveColocation(): bool
    {
        return $this->adhesions()
            ->whereNull('left_at')
            ->whereHas('colocation', fn($q) => $q->where('statut', 'active'))
            ->exists();
    }

    public function colocations()
    {
        return $this->belongsToMany(Colocation::class, 'adhesions', 'utilisateur_id', 'colocation_id')
            ->withPivot(['role_dans_colocation', 'date_adhesion', 'left_at'])
            ->withTimestamps();
    }

    public function depenses()
    {
        return $this->hasMany(Depense::class, 'payeur_id');
    }

    public function paiementsEffectues()
    {
        return $this->hasMany(Paiement::class, 'payeur_id');
    }

    public function paiementsRecus()
    {
        return $this->hasMany(Paiement::class, 'beneficiaire_id');
    }
}

