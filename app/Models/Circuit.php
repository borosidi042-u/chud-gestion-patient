<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Circuit extends Model
{
    protected $fillable = ['patient_id', 'service_id', 'user_id', 'is_entry', 'is_exit'];

    protected $casts = [
        'is_entry' => 'boolean',
        'is_exit' => 'boolean',
    ];

    public function patient() { return $this->belongsTo(Patient::class); }
    public function service() { return $this->belongsTo(Service::class); }
    public function user()    { return $this->belongsTo(User::class); }

    // Événement pour gérer la cohérence des débuts et fins de visite
    protected static function booted()
    {
        static::creating(function ($circuit) {
            // Si c'est le premier circuit du patient, marquer comme début
            $count = Circuit::where('patient_id', $circuit->patient_id)->count();
            if ($count === 0) {
                $circuit->is_entry = true;
            }
        });
    }
}
