<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Visite extends Model
{
    protected $fillable = [
        'patient_id', 'numero_visite', 'date_debut', 'date_fin', 'statut', 'validated_by'
    ];

    protected $casts = [
        'date_debut' => 'datetime',
        'date_fin' => 'datetime',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function mouvements()
    {
        return $this->hasMany(Mouvement::class);
    }

    public function validatedBy()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function getTimeline()
    {
        return $this->mouvements()
            ->with(['service', 'salle', 'lit', 'agent'])
            ->orderBy('heure_arrivee', 'asc')
            ->get();
    }

    public function getDuree(): string
    {
        $debut = $this->date_debut;
        $fin = $this->date_fin ?? now();

        $diff = $debut->diff($fin);

        $parties = [];
        if ($diff->d > 0) $parties[] = $diff->d . ' jour(s)';
        if ($diff->h > 0) $parties[] = $diff->h . ' heure(s)';
        if ($diff->i > 0) $parties[] = $diff->i . ' minute(s)';

        if (empty($parties)) {
            return 'moins d\'une minute';
        }

        return implode(' et ', $parties);
    }

    public function close(int $userId): void
    {
        $this->update([
            'statut' => 'terminee',
            'date_fin' => now(),
            'validated_by' => $userId,
        ]);
    }

    public function getLitActuel(): ?Lit
    {
        $dernierMouvement = $this->mouvements()
            ->whereNotNull('lit_id')
            ->where('type', '!=', 'sortie')
            ->latest('heure_arrivee')
            ->first();

        if ($dernierMouvement && $dernierMouvement->lit) {
            return $dernierMouvement->lit;
        }

        return null;
    }
}
