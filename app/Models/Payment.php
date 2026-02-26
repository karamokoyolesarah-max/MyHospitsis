<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'transaction_ref',
        'amount',
        'currency',
        'buyer_type',
        'buyer_id',
        'plan_id',
        'status',
        'payment_method',
        'metadata',
        'response',
        'appointment_id',
        'patient_id',
        'payment_date',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'metadata' => 'array',
        'response' => 'array',
        'payment_date' => 'datetime',
    ];

    // Relationships
    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($payment) {
            if (empty($payment->transaction_ref)) {
                $payment->transaction_ref = 'PAY' . str_pad(Payment::count() + 1, 6, '0', STR_PAD_LEFT);
            }
        });
    }
}