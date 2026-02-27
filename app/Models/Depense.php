<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Depense extends Model
{
    protected $fillable = [
        'titre',
        'montant',
        'date_depense',
        'categorie_id',
        'payeur_id',
        'colocation_id',
    ];

    protected function casts(): array
    {
        return [
            'date_depense' => 'date',
            'montant' => 'decimal:2',
        ];
    }

    public function payeur()
    {
        return $this->belongsTo(User::class, 'payeur_id');
    }

    public function colocation()
    {
        return $this->belongsTo(Colocation::class);
    }

    public function categorie()
    {
        return $this->belongsTo(Categorie::class);
    }
}
