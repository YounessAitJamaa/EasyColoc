<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Invitation extends Model
{
    protected $fillable = [
        'colocation_id',
        'email',
        'token',
        'statut',
        'expire_le',
    ];

    protected function casts(): array
    {
        return [
            'expire_le' => 'datetime',
        ];
    }

    public function colocation()
    {
        return $this->belongsTo(Colocation::class);
    }

    // generer token unique pour l'invitation

    public static function genererToken(): string
    {
        return Str::random(64);
    }

    // verifier si l'invitation est expiree 

    public function estExpiree(): bool
    {
        return $this->expire_le && $this->expire_le->isPast();
    }
}
