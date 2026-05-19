<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lit extends Model
{
    protected $fillable = [
        'salle_id', 'numero', 'statut', 'patient_id'
    ];

    protected $casts = [
        'statut' => 'string',
    ];

    public function salle()
    {
        return $this->belongsTo(Salle::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function isFree(): bool
    {
        return $this->statut === 'libre';
    }

    public function occupy(int $patientId): void
    {
        $this->update([
            'statut' => 'occupe',
            'patient_id' => $patientId,
        ]);
    }

    public function release(): void
    {
        $this->update([
            'statut' => 'libre',
            'patient_id' => null,
        ]);
    }
}
