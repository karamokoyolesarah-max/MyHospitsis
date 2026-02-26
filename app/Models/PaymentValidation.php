<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentValidation extends Model
{
    protected $fillable = [
        'hospital_id',
        'invoice_id',
        'payment_reference',
        'mobile_operator',
        'mobile_number',
        'amount',
        'validated_by',
        'validated_at',
        'notes'
    ];

    protected $casts = [
        'validated_at' => 'datetime',
        'amount' => 'decimal:2'
    ];

    /**
     * Relation avec la facture
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Relation avec l'utilisateur qui a validé (caissière)
     */
    public function validator()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    /**
     * Relation avec l'hôpital
     */
    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }

    /**
     * Scope pour filtrer par hôpital
     */
    public function scopeForHospital($query, $hospitalId)
    {
        return $query->where('hospital_id', $hospitalId);
    }

    /**
     * Scope pour filtrer par opérateur
     */
    public function scopeByOperator($query, $operator)
    {
        return $query->where('mobile_operator', $operator);
    }
}
