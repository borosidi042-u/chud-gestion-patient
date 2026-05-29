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

    public function getFullNameAttribute()
    {
        return $this->prenom . ' ' . $this->nom;
    }

    public function getRoleLabelAttribute()
    {
        return match($this->role) {
            'admin' => 'Administrateur',
            'infirmier' => 'Infirmier',
            default => 'Agent d\'accueil',
        };
    }
    public function circuits()
    {
        return $this->hasMany(Circuit::class);
    }

    public function patients()
    {
        return $this->hasMany(Patient::class);
    }

    public function mouvements()
    {
        return $this->hasMany(Mouvement::class, 'agent_id');
    }

    public function visitesValidees()
    {
        return $this->hasMany(Visite::class, 'validated_by');
    }

    public function isApproved()
    {
        return $this->approved;
    }
}
