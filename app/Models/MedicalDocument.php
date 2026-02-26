<?php

namespace App\Models;

use App\Traits\BelongsToHospital;
use Illuminate\Database\Eloquent\{Model, Factories\HasFactory, SoftDeletes};
class MedicalDocument extends Model
{
    use HasFactory, SoftDeletes;
     use BelongsToHospital;
     
    protected $fillable = [
       'hospital_id', 'patient_id', 'uploaded_by_id', 'document_type', 'title',
        'file_path', 'file_name', 'mime_type', 'file_size',
        'is_validated', 'validated_by_id', 'validated_at',
        'version', 'parent_document_id',        'is_visible_to_patient',
        'medecin_externe_id',
    ];

    protected $casts = [
        'validated_at' => 'datetime',
        'is_validated' => 'boolean',
        'file_size' => 'integer',
        'version' => 'integer',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by_id');
    }

    public function medecinExterne()
    {
        return $this->belongsTo(MedecinExterne::class, 'medecin_externe_id');
    }

    public function validatedBy()
    {
        return $this->belongsTo(User::class, 'validated_by_id');
    }

    public function parentDocument()
    {
        return $this->belongsTo(MedicalDocument::class, 'parent_document_id');
    }

    public function versions()
    {
        return $this->hasMany(MedicalDocument::class, 'parent_document_id');
    }

    public function scopeValidated($query)
    {
        return $query->where('is_validated', true);
    }

    public function getDocumentTypeLabelAttribute(): string
    {
        $labels = [
            'lab_result' => 'Résultat Laboratoire',
            'imaging' => 'Imagerie',
            'report' => 'Compte-Rendu',
            'discharge_summary' => 'Résumé de Sortie',
            'consent' => 'Consentement',
        ];

        return $labels[$this->document_type] ?? $this->document_type;
    }

    public function getFileSizeHumanAttribute(): string
    {
        $size = $this->file_size;
        $units = ['o', 'Ko', 'Mo', 'Go'];
        
        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }
        
        return round($size, 2) . ' ' . $units[$i];
    }
}