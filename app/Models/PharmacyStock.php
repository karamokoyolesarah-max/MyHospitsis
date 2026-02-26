<?php

namespace App\Models;

use App\Traits\BelongsToHospital;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PharmacyStock extends Model
{
    use HasFactory, BelongsToHospital;

    protected $fillable = [
        'hospital_id',
        'medication_id',
        'quantity',
        'min_threshold',
        'batch_number',
        'expiry_date',
        'location',
    ];

    protected $casts = [
        'expiry_date' => 'date',
    ];

    public function medication()
    {
        return $this->belongsTo(Medication::class);
    }

    public function logs()
    {
        return $this->hasMany(PharmacyStockLog::class);
    }
}
