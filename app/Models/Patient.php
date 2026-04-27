<?php
// ============================================================
// FICHIER 1 : app/Models/Patient.php
// ============================================================
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    protected $fillable = [
        'code_unique', 'nom', 'prenom',
        'date_naissance', 'adresse', 'telephone', 'npi', 'user_id',
    ];

    protected $casts = [
        'date_naissance' => 'date',
    ];

    // L'agent qui a créé le patient
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Toutes les factures du patient
    public function factures()
    {
        return $this->hasMany(Facture::class);
    }

    // Tout le circuit du patient
    public function circuits()
    {
        return $this->hasMany(Circuit::class);
    }

    // Nom complet formaté
    public function getNomCompletAttribute(): string
    {
        return $this->prenom . ' ' . $this->nom;
    }
}
