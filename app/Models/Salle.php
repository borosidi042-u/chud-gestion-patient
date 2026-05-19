<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Salle extends Model
{
    protected $fillable = [
        'service_id', 'nom', 'description', 'capacite'
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function lits()
    {
        return $this->hasMany(Lit::class);
    }

    public function getFreeLits()
    {
        return $this->lits()->where('statut', 'libre')->get();
    }
}
