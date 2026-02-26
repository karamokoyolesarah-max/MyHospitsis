<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medication extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'brand_name',
        'active_ingredient',
        'therapeutic_class',
        'form',
        'dosage',
        'manufacturer',
        'category',
        'unit_price',
        'description',
        'is_active',
    ];

    public function stocks()
    {
        return $this->hasMany(PharmacyStock::class);
    }
}
