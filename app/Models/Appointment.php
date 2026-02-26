<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne; 
use App\Models\Service;
use App\Models\Patient;
use App\Models\User;
use App\Models\Prestation;
use App\Models\Invoice;
use App\Traits\BelongsToHospital;

class Appointment extends Model
{
    use HasFactory, BelongsToHospital;

    protected $fillable = [
        'hospital_id', 'patient_id', 'doctor_id', 'service_id',
        'appointment_datetime', 'duration', 'status', 'type',
        'reason', 'notes', 'consultation_type', 'home_address', 'cashier_id',
        'payment_transaction_id', 'payment_method', 'payment_operator',
        'medecin_externe_id', 'doctor_current_latitude', 'doctor_current_longitude',
        'estimated_arrival_time', 'travel_started_at', 'travel_completed_at',
        'calculated_distance_km', 'calculated_travel_fee', 'tax_amount', 'total_amount',
        'patient_confirmation_start_at', 'patient_confirmation_end_at', 'rating_stars', 'rating_comment',
        'secretary_archived_at'
    ];

    protected $casts = [
        'appointment_datetime' => 'datetime',
        'secretary_archived_at' => 'datetime',
    ];

    public function patient(): BelongsTo {
        return $this->belongsTo(Patient::class)->withoutGlobalScopes(['hospital', 'hospital_filter']);
    }

    public function doctor(): BelongsTo {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function service(): BelongsTo {
        return $this->belongsTo(Service::class);
    }

    public function prestations(): BelongsToMany {
        return $this->belongsToMany(Prestation::class, 'appointment_prestations')
                    ->withPivot('quantity', 'unit_price', 'total', 'added_at', 'added_by')
                    ->withTimestamps();
    }

    // Un RDV peut avoir plusieurs factures (le lien appointment_id est dans la table invoices)
    public function invoices() {
        return $this->hasMany(\App\Models\Invoice::class, 'appointment_id');
    }

    // Relation pour accéder à la dernière facture (pour compatibilité avec la vue)
    public function invoice() {
        return $this->hasOne(\App\Models\Invoice::class, 'appointment_id')->latest();
    }

    public function cashier(): BelongsTo {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function medecinExterne(): BelongsTo {
        return $this->belongsTo(MedecinExterne::class, 'medecin_externe_id');
    }
}