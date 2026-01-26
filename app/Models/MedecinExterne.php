<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class MedecinExterne extends Authenticatable
{
    use Notifiable;

    protected $table = 'medecins_externes';

    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'telephone',
        'specialite',
        'numero_ordre',
        'adresse_cabinet',
        'adresse_residence',
        'diplome_path',
        'id_card_recto_path',
        'id_card_verso_path',
        'password',
        'statut',
        'email_verified_at',
        'role',
        'is_available',
        'balance',
        'current_plan',
        'plan_expires_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_available' => 'boolean',
        'balance' => 'decimal:2',
        'plan_expires_at' => 'datetime',
    ];

    // Accessor for name
    public function getNameAttribute()
    {
        return $this->prenom . ' ' . $this->nom;
    }

    // Relationship with wallet
    public function wallet()
    {
        return $this->hasOne(SpecialistWallet::class, 'specialist_id');
    }

    // Relationship with prestations
    public function prestations()
    {
        return $this->hasMany(ExternalDoctorPrestation::class, 'medecin_externe_id');
    }

    // Relationship with recharges
    public function recharges()
    {
        return $this->hasMany(ExternalDoctorRecharge::class, 'medecin_externe_id');
    }

    // Check if plan is active (Monthly activation paid)
    public function hasPlanActive()
    {
        return $this->plan_expires_at && $this->plan_expires_at->isFuture();
    }
}