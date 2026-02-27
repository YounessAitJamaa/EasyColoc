<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Colocation extends Model
{
    protected $fillable = [
        'nom',
        'description',
        'statut',
        'date_annulation',
        'cree_par',
    ];

    protected function casts(): array
    {
        return [
            'date_annulation' => 'datetime',
        ];
    }

    public function createur()
    {
        return $this->belongsTo(User::class, 'cree_par');
    }

    public function adhesions()
    {
        return $this->hasMany(Adhesion::class);
    }

    public function membres()
    {
        return $this->belongsToMany(User::class, 'adhesions', 'colocation_id', 'utilisateur_id')
            ->withPivot(['role_dans_colocation', 'date_adhesion', 'left_at'])
            ->withTimestamps();
    }

    public function depenses()
    {
        return $this->hasMany(Depense::class);
    }

    public function categories()
    {
        return $this->hasMany(Categorie::class);
    }

    public function invitations()
    {
        return $this->hasMany(Invitation::class);
    }

    public function paiements()
    {
        return $this->hasMany(Paiement::class);
    }
}
