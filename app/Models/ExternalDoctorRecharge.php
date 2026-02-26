<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExternalDoctorRecharge extends Model
{
    protected $fillable = [
        'medecin_externe_id',
        'amount',
        'payment_method',
        'transaction_id',
        'phone_number',
        'status',
        'response_data',
        // CinetPay integration fields
        'cinetpay_transaction_id',
        'payment_token',
        'cinetpay_response',
        // Failure handling
        'failure_reason',
        // SMS tracking
        'sms_sent_at',
        // Wave manual validation
        'requires_manual_validation',
        'validated_by',
        'validated_at',
        'validation_notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'cinetpay_response' => 'array',
        'sms_sent_at' => 'datetime',
        'validated_at' => 'datetime',
        'requires_manual_validation' => 'boolean',
    ];

    public function medecinExterne()
    {
        return $this->belongsTo(MedecinExterne::class, 'medecin_externe_id');
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

    public function scopeWavePending($query)
    {
        return $query->where('requires_manual_validation', true)
                     ->where('status', 'pending');
    }
}
