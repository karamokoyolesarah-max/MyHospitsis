<?php

namespace App\Models;

use App\Traits\BelongsToHospital;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LabRequest extends Model
{
    use HasFactory, BelongsToHospital;

    protected $fillable = [
        'hospital_id',
        'patient_vital_id',
        'patient_ipu',
        'patient_name',
        'doctor_id',
        'service_id',
        'test_name',
        'test_category',
        'clinical_info',
        'status',
        'lab_technician_id',
        'biologist_id',
        'result',
        'result_data',
        'result_file',
        'is_paid', // Added
        'payment_transaction_id',
        'payment_method',
        'payment_operator',
        'requested_at',
        'sample_received_at',
        'completed_at',
        'validated_at',
        'is_visible_to_patient',
        'medecin_externe_id',
    ];

    protected $casts = [
        'result_data' => 'array',
        'requested_at' => 'datetime',
        'sample_received_at' => 'datetime',
        'completed_at' => 'datetime',
        'validated_at' => 'datetime',
    ];

    public function patientVital()
    {
        return $this->belongsTo(PatientVital::class);
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function medecinExterne()
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

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function labTechnician()
    {
        return $this->belongsTo(User::class, 'lab_technician_id');
    }

    public function biologist()
    {
        return $this->belongsTo(User::class, 'biologist_id');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
}
