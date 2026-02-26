<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Hospital;
use App\Models\Service;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $hospitals = Hospital::all();

        foreach ($hospitals as $hospital) {
            // Service Pharmacie
            Service::firstOrCreate(
                [
                    'hospital_id' => $hospital->id,
                    'type' => 'pharmacy'
                ],
                [
                    'name' => 'Pharmacie Centrale',
                    'code' => 'PHARM-' . $hospital->id,
                    'description' => 'Gestion des stocks et dispensation des médicaments.',
                    'color' => '#10b981', // Emerald
                    'icon' => 'pill',
                    'is_active' => true,
                    'consultation_price' => 0
                ]
            );

            // Service Administration
            Service::firstOrCreate(
                [
                    'hospital_id' => $hospital->id,
                    'type' => 'administrative'
                ],
                [
                    'name' => 'Secrétariat Général',
                    'code' => 'ADMIN-' . $hospital->id,
                    'description' => 'Direction, secrétariat et gestion administrative.',
                    'color' => '#6366f1', // Indigo
                    'icon' => 'building',
                    'is_active' => true,
                    'consultation_price' => 0
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // On ne supprime pas forcément les services en rollback pour éviter de casser des relations utilisateurs
    }
};
