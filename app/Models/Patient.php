<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Patient extends Model
{
    protected $fillable = [
        'nom', 'prenom', 'code_unique', 'date_naissance', 'adresse', 'telephone', 'npi', 'user_id'
    ];

    protected $casts = [
        'date_naissance' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function circuits()
    {
        return $this->hasMany(Circuit::class);
    }

    public function factures()
    {
        return $this->hasMany(Facture::class);
    }

    public function visites()
    {
        return $this->hasMany(Visite::class);
    }

    public function mouvements()
    {
        return $this->hasMany(Mouvement::class);
    }

    public function getNomCompletAttribute(): string
    {
        return $this->prenom . ' ' . $this->nom;
    }

    public function getVisiteEnCours(): ?Visite
    {
        return $this->visites()->where('statut', 'en_cours')->latest()->first();
    }

    public function getLitActuel(): ?Lit
    {
        $visite = $this->getVisiteEnCours();
        if (!$visite) {
            return null;
        }
        return $visite->getLitActuel();
    }
    // À ajouter dans la classe Patient
    public static function isTelephoneUnique($telephone, $excludeId = null)
    {
        $query = self::where('telephone', $telephone);
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        return !$query->exists();
    }
}
