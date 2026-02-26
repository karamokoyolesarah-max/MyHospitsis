<?php

namespace App\Models;
use App\Traits\BelongsToHospital;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PatientVital extends Model
{
    use HasFactory, BelongsToHospital;

    /**
     * Les attributs qui peuvent être remplis massivement.
     */
    protected $fillable = [
        'patient_name',
        'patient_ipu',
        'temperature',
        'pulse',
        'blood_pressure',
        'weight',
        'height',
        'urgency',
        'reason',
        'notes',
        'user_id',
        'doctor_id',   // Nouveau champ d'assignation
        'hospital_id', // Ajouté par sécurité
        'service_id',  // Ajouté par sécurité
        'blood_group',
        'allergies',
        'medical_history',
        'observations',
        'ordonnance',
        'custom_vitals',
        'status',
        'meta',
        'is_visible_to_patient',
        'medecin_externe_id',
    ];

    protected $casts = [
        'custom_vitals' => 'array',
        'meta' => 'array',
        'is_visible_to_patient' => 'boolean',
    ];

    /**
     * Relation : Un dossier de constantes appartient à un service.
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Relation : Un dossier de constantes appartient à une infirmière (User).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relation : Un dossier de constantes peut être assigné à un médecin.
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    /**
     * Relation : Un dossier de constantes peut être assigné à un médecin externe.
     */
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

    /**
     * Relation : Un dossier de constantes appartient à un patient.
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'patient_ipu', 'ipu');
    }

    /**
     * Relation : Un dossier de constantes est assigné à une chambre.
     */
    public function room(): HasOne
    {
        return $this->hasOne(Room::class, 'patient_vital_id');
    }




    /**
     * Relation : Les demandes d'analyses/examens associées.
     */
    public function labRequests()
    {
        return $this->hasMany(LabRequest::class, 'patient_vital_id');
    }

    /**
     * Accesseur : Récupérer l'admission correspondant à la date de ce dossier.
     * Cela permet de retrouver le médecin traitant lors de l'hospitalisation.
     */
    public function getRelatedAdmissionAttribute()
    {
        // On cherche une admission qui était active au moment de la création de ce dossier
        // Soit créée avant ce dossier et non finie
        // Soit créée avant ce dossier et finie après ce dossier
        // AJUSTEMENT : On inclut une tolérance de 24h pour les dossiers créés avant l'admission (Ex: Consultations initiales)
        return \App\Models\Admission::withoutGlobalScope('hospital_filter') 
            ->where('patient_id', function($q) {
                 $q->select('id')->from('patients')->where('ipu', $this->patient_ipu)->limit(1);
            })
            // L'admission a commencé AVANT (Dossier + 24h). 
            // Donc si Dossier est à 00h, on accepte une admission qui commence jusqu'à 24h le lendemain (ou le jour même plus tard).
            ->where('admission_date', '<=', $this->created_at->addHours(24))
            ->where(function($query) {
                $query->whereNull('discharge_date')
                      ->orWhere('discharge_date', '>=', $this->created_at);
            })
            ->latest('admission_date')
            ->first();
    }

    /**
     * Accesseur : Récupérer le nom de la prestation liée au RDV du jour
     */
    public function getPrestationAttribute()
    {
        // On cherche le RDV traité ce jour-là pour ce patient
        $appt = \App\Models\Appointment::whereHas('patient', function($q) {
                $q->where('ipu', $this->patient_ipu);
            })
            ->where('hospital_id', $this->hospital_id)
            ->where(function($q) {
                // Soit le RDV était prévu aujourd'hui, soit il a été modifié (traité) aujourd'hui
                $q->whereDate('appointment_datetime', $this->created_at->toDateString())
                  ->orWhereDate('updated_at', $this->created_at->toDateString());
            })
            ->with('prestations')
            ->latest('updated_at')
            ->first();
            
        return ($appt && $appt->prestations->isNotEmpty()) 
            ? $appt->prestations->pluck('name')->implode(', ') 
            : 'Consultation Standard';
    }
}