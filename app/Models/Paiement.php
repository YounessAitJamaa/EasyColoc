<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Paiement extends Model
{
    protected $fillable = [
        'colocation_id',
        'payeur_id',
        'beneficiaire_id',
        'montant',
    ];

    protected function casts(): array
    {
        return [
            'montant' => 'decimal:2',
        ];
    }

    public function colocation()
    {
        return $this->belongsTo(Colocation::class);
    }

    public function payeur()
    {
        return $this->belongsTo(User::class, 'payeur_id');
    }

    public function beneficiaire()
    {
        return $this->belongsTo(User::class, 'beneficiaire_id');
    }
}
