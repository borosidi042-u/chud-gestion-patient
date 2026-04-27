<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = ['nom_service', 'description'];

    public function circuits()  { return $this->hasMany(Circuit::class); }
    public function factures()  { return $this->hasMany(Facture::class); }
}
