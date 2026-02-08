<?php

namespace App\Services\Insurance;

use App\Models\InsuranceVerificationLog;
use Illuminate\Support\Facades\Auth;

class InsuranceService
{
    protected $provider;

    public function __construct()
    {
        // Flexible: can be changed via config/env or database in future
        $providerClass = config('insurance.provider', MockInsuranceProvider::class);
        $this->provider = new $providerClass();
    }

    public function verify(string $matricule): array
    {
        $result = $this->provider->verifyRights($matricule);

        // Logging the attempt
        InsuranceVerificationLog::create([
            'hospital_id' => Auth::user()?->hospital_id ?? 1, // Fallback to 1 for dev
            'matricule' => $matricule,
            'status' => $result['status'] ?? 'inconnu',
            'provider_name' => $this->provider->getName(),
            'response_message' => $result['message'] ?? ($result['status'] === 'valide' ? 'Vérification réussie' : 'Échec de vérification'),
        ]);

        return $result;
    }

    public function getProviderName(): string
    {
        return $this->provider->getName();
    }
}
