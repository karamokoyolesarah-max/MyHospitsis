<?php

namespace App\Models;

use App\Traits\BelongsToHospital;
use Illuminate\Database\Eloquent\{Model, Factories\HasFactory, SoftDeletes};

class MedicalRecord extends Model
{
    use HasFactory, BelongsToHospital;

    protected $fillable = [
        'hospital_id', 'patient_id', 'recorded_by_id', 'record_type', 'content',
        'record_datetime', 'is_validated', 'validated_by_id', 'validated_at', 'status', 'meta',
    ];

    protected $casts = [
        'record_datetime' => 'datetime',
        'validated_at' => 'datetime',
        'is_validated' => 'boolean',
        'meta' => 'array',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by_id');
    }

    public function validatedBy()
    {
        return $this->belongsTo(User::class, 'validated_by_id');
    }

    public function scopeValidated($query)
    {
        return $query->where('is_validated', true);
    }

    public function getRecordTypeLabelAttribute(): string
    {
        $labels = [
            'consultation' => 'Consultation',
            'diagnosis' => 'Diagnostic',
            'history' => 'Antécédents',
            'note' => 'Note',
            'report' => 'Compte-Rendu',
        ];

        return $labels[$this->record_type] ?? $this->record_type;
    }
}