<?php

namespace App\Models;

use App\Models\PatientVital; 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;
use App\Models\Admission;
use App\Models\Appointment;
use App\Models\MedicalRecord;
use App\Models\Prescription;
use App\Models\ClinicalObservation;
use App\Models\MedicalDocument;
use App\Traits\BelongsToHospital;

class Patient extends Authenticatable
{
    use HasFactory, SoftDeletes, Notifiable, BelongsToHospital;

    protected $fillable = [
        'hospital_id','ipu', 'name', 'first_name', 'dob', 'gender', 
        'address', 'city', 'postal_code', 'phone', 'email',
        'emergency_contact_name', 'emergency_contact_phone',
        'blood_group', 'allergies', 'medical_history', 'is_active',
        'password',
        'referring_doctor_id',
        // Geolocalisation pour consultations a domicile
        'latitude',
        'longitude',
        'formatted_address',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'dob' => 'date',
        'allergies' => 'array',
        'is_active' => 'boolean',
        'password' => 'hashed',
    ];



    // --- RELATIONS ---
    public function admissions()
    {
        return $this->hasMany(Admission::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function medicalRecords()
    {
        return $this->hasMany(MedicalRecord::class);
    }

    public function prescriptions()
    {
        return $this->hasMany(Prescription::class);
    }

    public function clinicalObservations()
    {
        return $this->hasMany(ClinicalObservation::class);
    }

    public function documents()
    {
        return $this->hasMany(MedicalDocument::class);
    }
    
    public function referringDoctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referring_doctor_id');
    }

    public function vitals()
    {
        return $this->hasMany(PatientVital::class, 'patient_ipu', 'ipu');
    }

    public function labRequests()
    {
        return $this->hasMany(LabRequest::class, 'patient_ipu', 'ipu');
    }

    // --- ACCESSEURS ---
    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->name;
    }

    public function getAgeAttribute(): int
    {
        return $this->dob ? $this->dob->age : 0;
    }

    // --- MÉTHODES STATIQUES ---
    public static function generateIpu(): string
    {
        do {
            $ipu = 'PAT' . date('Y') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
        } while (self::withoutGlobalScopes()->where('ipu', $ipu)->exists());
        
        return $ipu;
    }

    // --- MÉTHODES D'AUTHENTIFICATION ---
    public function getAuthIdentifierName()
    {
        return 'id'; // ✅ Utilise l'ID, pas l'email
    }

    public function getAuthIdentifier()
    {
        return $this->getKey();
    }

    public function getAuthPassword()
    {
        return $this->password;
    }

    // --- RÔLES STUBS (Pour la compatibilité avec les layouts partagés) ---
    public function isDoctor(): bool { return false; }
    public function isNurse(): bool { return false; }
    public function isAdmin(): bool { return false; }
    public function isCashier(): bool { return false; }
    public function isInternalDoctor(): bool { return false; }
    public function isExternalDoctor(): bool { return false; }
    public function isDoctorLab(): bool { return false; }
    public function isLabTechnician(): bool { return false; }
    public function isTechnical(): bool { return false; }
    public function isMedical(): bool { return false; }
    public function isAdministrative(): bool { return false; }
    public function isPharmacist(): bool { return false; }
    public function isSecretary(): bool { return false; }
    public function hasRole($role): bool { return false; }

    /**
     * Get French label for user role (stub for Patient)
     */
    public function getRoleLabel(): string
    {
        return 'Patient';
    }
}