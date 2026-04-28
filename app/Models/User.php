<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'nom', 'prenom', 'email', 'password', 'role', 'approved'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'approved' => 'boolean',
    ];

    // Accesseur pour le nom complet
    public function getFullNameAttribute()
    {
        return $this->prenom . ' ' . $this->nom;
    }

    // Relation avec les factures
    public function factures()
    {
        return $this->hasMany(Facture::class);
    }

    // Relation avec les circuits
    public function circuits()
    {
        return $this->hasMany(Circuit::class);
    }

    // Vérifier si l'utilisateur est approuvé
    public function isApproved()
    {
        return $this->approved;
    }
}
