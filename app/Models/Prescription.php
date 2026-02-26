<?php

namespace App\Models;
use App\Traits\BelongsToHospital;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Prescription extends Model
{   use BelongsToHospital;
    
    protected $fillable = [
        'hospital_id',
        'patient_id',
        'doctor_id',
        'medication',
        'dosage',
        'frequency',
        'start_date',
        'end_date',
        'instructions',
        'is_signed',
        'signed_at',
        'signature_hash',
        'allergy_checked',
        'status',
        'is_visible_to_patient',
        'category',
        'medecin_externe_id',
    ];

    protected $casts = [
        'start_date'      => 'date',
        'end_date'        => 'date',
        'signed_at'       => 'datetime',
        'is_signed'       => 'boolean',
        'allergy_checked' => 'boolean',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function medecinExterne(): BelongsTo
    {
        return $this->belongsTo(MedecinExterne::class, 'medecin_externe_id');
    }

    public function getDoctorNameAttribute(): string
    {
        if ($this->medecinExterne) {
            return 'Dr ' . $this->medecinExterne->prenom . ' ' . $this->medecinExterne->nom;
        }
        if ($this->doctor) {
            return 'Dr ' . $this->doctor->name;
        }
        return 'Médecin inconnu';
    }

    public function hospital(): BelongsTo
    {
        return $this->belongsTo(Hospital::class);
    }

    // Relation pour les items (si vous utilisez une table de détails séparée)
    public function items() 
    {
        return $this->hasMany(PrescriptionItem::class);
    }
}