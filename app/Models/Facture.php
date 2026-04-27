<?php
// app/Models/Facture.php
// ── Remplace l'ancien modèle Facture.php ──────────────────────────────────

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Facture extends Model
{
    protected $fillable = [
        'patient_id','numero_facture','montant','date_facture',
        'service_id','user_id',
        'pec_organisme','pec_montant',   // ← nouveaux champs
    ];

    protected $casts = [
        'date_facture' => 'date',
        'montant'      => 'decimal:2',
        'pec_montant'  => 'decimal:2',
    ];

    public function patient() { return $this->belongsTo(Patient::class); }
    public function service() { return $this->belongsTo(Service::class); }
    public function user()    { return $this->belongsTo(User::class); }

    // ── Accesseurs calculés ───────────────────────────────────────────────
    // Montant à la charge du patient = total - prise en charge
    public function getMontantPatientAttribute(): float
    {
        return floatval($this->montant) - floatval($this->pec_montant ?? 0);
    }

    // Indique si une prise en charge existe
    public function getHasPECAttribute(): bool
    {
        return !empty($this->pec_organisme) && floatval($this->pec_montant) > 0;
    }
}
