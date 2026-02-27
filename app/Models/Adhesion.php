<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Adhesion extends Model
{
    protected $fillable = [
        'utilisateur_id',
        'colocation_id',
        'role_dans_colocation',
        'date_adhesion',
        'left_at',
    ];

    protected function casts(): array
    {
        return [
            'date_adhesion' => 'datetime',
            'left_at' => 'datetime',
        ];
    }

    public function utilisateur()
    {
        return $this->belongsTo(User::class, 'utilisateur_id');
    }

    public function colocation()
    {
        return $this->belongsTo(Colocation::class);
    }
}
