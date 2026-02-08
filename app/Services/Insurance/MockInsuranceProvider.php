<?php

namespace App\Services\Insurance;

class MockInsuranceProvider implements InsuranceProviderInterface
{
    public function verifyRights(string $matricule): array
    {
        // Simulator logic:
        // Starts with 11 -> valid, 80% coverage
        // Starts with 00 -> expired
        
        if (str_starts_with($matricule, '11')) {
            return [
                'status' => 'valide',
                'couverture' => 80,
                'patient' => 'Patient Simulatif (CNAM)',
                'message' => 'Vérification réussie'
            ];
        }

        if (str_starts_with($matricule, '00')) {
            return [
                'status' => 'expiré',
                'couverture' => 0,
                'message' => 'Carte non valide'
            ];
        }

        return [
            'status' => 'inconnu',
            'couverture' => 0,
            'message' => 'Matricule non trouvé'
        ];
    }

    public function getName(): string
    {
        return 'MockInsurance';
    }
}
