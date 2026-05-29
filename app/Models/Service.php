<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'nom_service', 'description', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function circuits()
    {
        return $this->hasMany(Circuit::class);
    }

    public function salles()
    {
        return $this->hasMany(Salle::class);
    }

    public function mouvements()
    {
        return $this->hasMany(Mouvement::class);
    }
}
