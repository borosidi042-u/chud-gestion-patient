<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mouvement extends Model
{
    protected $fillable = [
        'visite_id', 'patient_id', 'service_id', 'salle_id', 'lit_id',
        'type', 'heure_arrivee', 'heure_depart', 'agent_id', 'note'
    ];

    protected $casts = [
        'heure_arrivee' => 'datetime',
        'heure_depart' => 'datetime',
    ];

    public function visite()
    {
        return $this->belongsTo(Visite::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function salle()
    {
        return $this->belongsTo(Salle::class);
    }

    public function lit()
    {
        return $this->belongsTo(Lit::class);
    }

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }
}
