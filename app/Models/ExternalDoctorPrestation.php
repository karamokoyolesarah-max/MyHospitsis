<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExternalDoctorPrestation extends Model
{
    protected $fillable = [
        'medecin_externe_id',
        'name',
        'description',
        'price',
        'commission_percentage',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'commission_percentage' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function medecinExterne()
    {
        return $this->belongsTo(MedecinExterne::class, 'medecin_externe_id');
    }
}
