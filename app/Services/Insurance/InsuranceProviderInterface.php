<?php

namespace App\Services\Insurance;

interface InsuranceProviderInterface
{
    /**
     * Verify insurance rights for a given matricule.
     *
     * @param string $matricule
     * @return array
     */
    public function verifyRights(string $matricule): array;

    /**
     * Get the provider name.
     *
     * @return string
     */
    public function getName(): string;
}
