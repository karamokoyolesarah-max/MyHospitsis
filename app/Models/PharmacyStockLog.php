<?php

namespace App\Models;

use App\Traits\BelongsToHospital;
use Illuminate\Database\Eloquent\Model;

class PharmacyStockLog extends Model
{
    use BelongsToHospital;

    protected $fillable = [
        'hospital_id',
        'pharmacy_stock_id',
        'user_id',
        'quantity',
        'type',
        'reason',
        'reference_id',
    ];

    public function stock()
    {
        return $this->belongsTo(PharmacyStock::class, 'pharmacy_stock_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
