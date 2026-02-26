<?php

namespace App\Models;

use App\Traits\BelongsToHospital;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class Service extends Model

{
     use HasFactory, BelongsToHospital;
    protected $fillable = ['name', 'code','hospital_id','description', 'consultation_price', 'form_config', 'diagnostic_config', 'admission_config', 'type', 'is_caisse', 'caisse_type', 'icon', 'color', 'location', 'parent_id'];

    protected $casts = [
        'form_config' => 'array',
        'diagnostic_config' => 'array',
        'admission_config' => 'array',
        'is_caisse' => 'boolean',
    ];

    // --- SCOPES ---
    public function scopeMedical($query)
    {
        return $query->where('type', 'medical');
    }

    public function scopeSupport($query)
    {
        return $query->where('type', 'support');
    }

    public function scopeTechnical($query)
    {
        return $query->where('type', 'technical');
    }

    // --- UTILITIES ---
    public function isMedical(): bool
    {
        return $this->type === 'medical';
    }

    public function isSupport(): bool
    {
        return $this->type === 'support';
    }

    public function isTechnical(): bool
    {
        return $this->type === 'technical';
    }

    // Un service peut avoir un parent (ex: Caisse liée à un Service Médical)
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'parent_id');
    }

    // Un service peut avoir des sous-services
    public function children(): HasMany
    {
        return $this->hasMany(Service::class, 'parent_id');
    }

    // Un service a plusieurs utilisateurs (Docteurs, Infirmiers)
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
    
    // Un service a plusieurs chambres/lits (table 'rooms')
    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    // Un service a plusieurs prestations
    public function prestations(): HasMany
    {
        return $this->hasMany(Prestation::class);
    }

    // Récupérer le prix de consultation pour ce service
    public function getConsultationPriceAttribute(): float
    {
        // 1. Priorité à la colonne 'consultation_price' si elle existe et n'est pas nulle
        if (array_key_exists('consultation_price', $this->attributes) && $this->attributes['consultation_price'] !== null) {
            return (float) $this->attributes['consultation_price'];
        }

        // 2. Sinon, on cherche la prestation de type consultation (en contournant le scope hospital)
        return $this->prestations()
                   ->withoutGlobalScope('hospital_filter')
                   ->where('category', 'consultation')
                   ->where('is_active', true)
                   ->first()?->price ?? 0;
    }
}
 