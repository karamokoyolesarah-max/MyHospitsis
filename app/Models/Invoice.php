<?php

namespace App\Models;

use App\Traits\BelongsToHospital;
use Illuminate\Database\Eloquent\{Model, Factories\HasFactory, SoftDeletes};
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Importation nécessaire

class Invoice extends Model
{
    use HasFactory, BelongsToHospital;

    protected $fillable = [
       'hospital_id', 'service_id', 'invoice_number', 'patient_id', 'appointment_id',
       'admission_id', 'lab_request_id', 'walk_in_consultation_id',
       'invoice_date', 'due_date', 'subtotal', 'tax',
       'status', 'paid_at', 'payment_method', 'payment_operator', 'notes',
       'insurance_name', 'insurance_card_number', 'insurance_coverage_rate',
       'insurance_settlement_status', // Added for recovery tracking
       'insurance_settled_at', 'insurance_settlement_reference',
       'cashier_id'
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function labRequest(): BelongsTo
    {
        return $this->belongsTo(LabRequest::class);
    }

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'paid_at' => 'datetime',
        'insurance_settled_at' => 'datetime',
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    // RELATION MANQUANTE : Lien avec le rendez-vous
    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class)->withTrashed();
    }

    public function admission(): BelongsTo
    {
        return $this->belongsTo(Admission::class);
    }

    public function walkInConsultation(): BelongsTo
    {
        return $this->belongsTo(WalkInConsultation::class);
    }
    
    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    // SCOPES
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    // ACCESSEURS
    public function getIsPaidAttribute(): bool
    {
        return $this->status === 'paid';
    }
}