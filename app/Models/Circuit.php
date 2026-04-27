<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Circuit extends Model
{
    protected $fillable = ['patient_id', 'service_id', 'user_id'];

    public function patient() { return $this->belongsTo(Patient::class); }
    public function service() { return $this->belongsTo(Service::class); }
    public function user()    { return $this->belongsTo(User::class); }
}
