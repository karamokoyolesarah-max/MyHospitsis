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
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function medecinExterne()
    {
        return $this->belongsTo(MedecinExterne::class, 'medecin_externe_id');
    }
}
