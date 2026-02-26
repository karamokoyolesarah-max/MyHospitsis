<?php

namespace App\Models;

use App\Traits\BelongsToHospital;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Service;
use App\Models\DoctorAvailability;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    use SoftDeletes;
    use BelongsToHospital;

    protected $fillable = [
        'hospital_id',
        'name',
        'email',
        'password',
        'role',
        'service_id',
        'is_active',
        'phone',
        'registration_number',
        'mfa_enabled',
        'mfa_secret',
        'profile_photo',
        'email_notifications',
        'sms_notifications',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'mfa_secret',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'mfa_enabled' => 'boolean',
            'email_notifications' => 'boolean',
            'sms_notifications' => 'boolean',
        ];
    }
    
    public function service(): BelongsTo 
    {
        return $this->belongsTo(Service::class); 
    }
    
    // --- NOUVELLES MÉTHODES DE RÔLE DOCTEUR ---
    
    // Vérifie si l'utilisateur est un médecin INTERNE
    public function isInternalDoctor(): bool
    {
        return $this->role === 'internal_doctor';
    }

    // Vérifie si l'utilisateur est un médecin EXTERNE (portail dédié)
    public function isExternalDoctor(): bool
    {
        return in_array($this->role, ['external_doctor', 'medecin_externe']);
    }

    // Vérifie si l'utilisateur est un DOCTEUR (Hôpital ou Technique)
    public function isDoctor(): bool
    {
        return in_array($this->role, ['doctor', 'medecin', 'internal_doctor', 'doctor_lab', 'doctor_radio']);
    }

    // Vérifie si l'utilisateur appartient au Pôle Technique
    public function isTechnical(): bool
    {
        if (in_array($this->role, ['doctor_lab', 'lab_technician', 'doctor_radio', 'radio_technician'])) {
            return true;
        }
        
        if (!$this->service) return false;

        if ($this->service->type === 'technical') return true;

        // Fallback sur le nom du service si le type n'est pas correct
        $serviceName = strtolower($this->service->name);
        return str_contains($serviceName, 'labo') || 
               str_contains($serviceName, 'biologi') || 
               str_contains($serviceName, 'imagerie') || 
               str_contains($serviceName, 'radio');
    }

    // Vérifie si l'utilisateur appartient au Pôle Médical (Soin)
    public function isMedical(): bool
    {
        // Une personne technique ou administrative/secrétariat n'est pas "Medical" au sens clinique
        if ($this->isTechnical() || in_array($this->role, ['secretary', 'cashier', 'administrative'])) return false;

        if (in_array($this->role, ['doctor', 'medecin', 'internal_doctor', 'nurse'])) {
            return true;
        }

        return $this->service && ($this->service->type === 'medical' || $this->service->type === 'support');
    }
    
    public function isAdministrative(): bool
    {
        return in_array($this->role, ['administrative', 'admin', 'secretary']);
    }
    
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isNurse(): bool
    {
        return $this->role === 'nurse';
    }

    public function isCashier(): bool
    {
        return $this->role === 'cashier';
    }

    public function isPharmacist(): bool
    {
        return $this->role === 'pharmacist';
    }

    public function isSecretary(): bool
    {
        return $this->role === 'secretary';
    }

    public function hasRole($role): bool
    {
        if ($role === 'doctor' && $this->role === 'medecin') return true;
        if ($role === 'medecin' && $this->role === 'doctor') return true;
        return $this->role === $role;
    }

    /**
     * Get French label for user role
     */
    public function getRoleLabel(): string
    {
        $roleLabels = [
            'admin' => 'Administrateur',
            'doctor' => 'Médecin',
            'medecin' => 'Médecin',
            'internal_doctor' => 'Médecin Interne',
            'external_doctor' => 'Médecin Externe',
            'doctor_lab' => 'Biologiste',
            'doctor_radio' => 'Radiologue',
            'lab_technician' => 'Technicien de Laboratoire',
            'radio_technician' => 'Technicien Radio',
            'nurse' => 'Infirmier(ère)',
            'cashier' => 'Caissier(ère)',
            'administrative' => 'Administratif',
            'receptionist' => 'Réceptionniste',
            'pharmacist' => 'Pharmacien(ne)',
            'secretary' => 'Secrétaire Général(e)',
        ];

        return $roleLabels[$this->role] ?? ucfirst(str_replace('_', ' ', $this->role));
    }

    public function availabilities()
    {
        return $this->hasMany(DoctorAvailability::class, 'doctor_id');
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'doctor_id');
    }

    // --- SCOPES DE PÔLES ---

    public function scopeMedical($query)
    {
        return $query->where(function($q) {
            // Primarily Medical roles
            $q->whereIn('role', ['nurse', 'receptionist', 'secretary', 'pharmacist', 'administrative', 'admin'])
              // OR Doctors NOT in specialized technical services
              ->orWhere(function($sq) {
                  $sq->whereIn('role', ['doctor', 'medecin', 'internal_doctor'])
                     ->where(function($ssq) {
                         $ssq->whereDoesntHave('service')
                            ->orWhereHas('service', function($sssq) {
                                $sssq->where('type', 'medical');
                            });
                     });
              });
        });
    }

    public function scopeTechnical($query)
    {
        return $query->where(function($q) {
            // Specialized technical roles
            $q->whereIn('role', ['doctor_lab', 'lab_technician', 'doctor_radio', 'radio_technician'])
              // OR Generic Doctors in Technical/Lab/Radio services
              ->orWhere(function($sq) {
                  $sq->whereIn('role', ['doctor', 'medecin', 'internal_doctor'])
                     ->whereHas('service', function($ssq) {
                         $ssq->where('type', 'technical')
                            ->orWhere('name', 'like', '%labo%')
                            ->orWhere('name', 'like', '%biologi%')
                            ->orWhere('name', 'like', '%imagerie%')
                            ->orWhere('name', 'like', '%radio%')
                            ->orWhere('name', 'like', '%analyse%');
                     });
              });
        });
    }

    public function scopeSupport($query)
    {
        // Strict role-based for support as per latest user feedback
        return $query->whereIn('role', ['cashier']);
    }
}