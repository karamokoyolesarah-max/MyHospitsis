<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Traits\BelongsToHospital;

class AuditLog extends Model
{    use BelongsToHospital;
    
    protected $fillable = [
        'hospital_id',
        'user_id',
        'action',
        'resource_type',
        'resource_id',
        'description',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'is_encrypted',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'is_encrypted' => 'boolean',
    ];

    public $timestamps = true;
    const UPDATED_AT = null;

    /**
     * Log an audit event
     */
    public static function log(string $action, string $resourceType, $resourceId = null, array $data = []): self
    {
        $log = new self();

        $log->user_id = Auth::id();
        $log->action = $action;
        $log->resource_type = $resourceType;
        $log->resource_id = $resourceId;
        $log->description = $data['description'] ?? null;
        $log->old_values = $data['old'] ?? null;
        $log->new_values = $data['new'] ?? null;
        $log->ip_address = request()->ip();
        $log->user_agent = request()->userAgent();

        $log->save();

        return $log;
    }
}
