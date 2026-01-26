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
        'reason', 'notes'
    ];

    protected $casts = [
        'appointment_datetime' => 'datetime',
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
                    ->withPivot('quantity', 'unit_price', 'total')
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
}