<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MedicalStaffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \App\Models\User::whereIn('role', ['doctor', 'nurse'])->delete();
        \App\Models\MedecinExterne::truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $hospitals = \App\Models\Hospital::all();
        $services = \App\Models\Service::all();

        if ($services->isEmpty()) {
            return;
        }

        $lastNames = ['Kouadio', 'Koné', 'Traoré', 'Bamba', 'Ouattara', 'N\'Guessan', 'Diabaté', 'Sanogo', 'Touré', 'Kouassi', 'Yao', 'Brou', 'Dibo', 'Zadi', 'Gnamien', 'Aka', 'N\'Dri', 'Koffi', 'Sidibé', 'Kamagaté'];
        $firstNamesM = ['Jean', 'Paul', 'Marc', 'David', 'Ibrahim', 'Seydou', 'Amadou', 'Koffi', 'Yao', 'Kouamé', 'Moussa', 'Abdoulaye', 'Oumar', 'Bakary', 'Christophe'];
        $firstNamesF = ['Marie', 'Sophie', 'Fatou', 'Mariam', 'Awa', 'Adjoua', 'Yasmine', 'Aïcha', 'Emma', 'Aya', 'Grace', 'Syntiche', 'Olivia', 'Sarah', 'Leila'];

        // 1. POPULATION DES MÉDECINS INTERNES (~2 par service)
        foreach ($services as $service) {
            for ($i = 1; $i <= 2; $i++) {
                $gender = rand(0, 1);
                $fName = $gender ? $firstNamesF[array_rand($firstNamesF)] : $firstNamesM[array_rand($firstNamesM)];
                $lName = $lastNames[array_rand($lastNames)];
                
                \App\Models\User::create([
                    'hospital_id' => $service->hospital_id,
                    'service_id' => $service->id,
                    'name' => "Dr. $fName $lName",
                    'email' => strtolower("dr.$fName.$lName." . rand(100, 999) . "@hospisis.ci"),
                    'password' => \Hash::make('password'),
                    'role' => 'doctor',
                    'phone' => '+225 07 ' . rand(10, 99) . ' ' . rand(10, 99) . ' ' . rand(10, 99),
                    'registration_number' => 'MED-' . strtoupper(substr($service->name, 0, 3)) . '-' . rand(1000, 9999),
                    'is_active' => true,
                ]);
            }

            // 2. POPULATION DES INFIRMIERS (1 par service)
            $gender = rand(0, 1);
            $fName = $gender ? $firstNamesF[array_rand($firstNamesF)] : $firstNamesM[array_rand($firstNamesM)];
            $lName = $lastNames[array_rand($lastNames)];
            
            \App\Models\User::create([
                'hospital_id' => $service->hospital_id,
                'service_id' => $service->id,
                'name' => ($gender ? 'Infirmière ' : 'Infirmier ') . "$fName $lName",
                'email' => strtolower("inf.$fName.$lName." . rand(100, 999) . "@hospisis.ci"),
                'password' => \Hash::make('password'),
                'role' => 'nurse',
                'phone' => '+225 05 ' . rand(10, 99) . ' ' . rand(10, 99) . ' ' . rand(10, 99),
                'is_active' => true,
            ]);
        }

        // 3. POPULATION DES MÉDECINS EXTERNES (Visite à domicile)
        $specialties = [
            'Cardiologie', 'Pédiatrie', 'Gynécologie', 'Dermatologie', 'Ophtalmologie', 
            'Médecine Générale', 'Neurologie', 'Kinésithérapie', 'Gastro-entérologie', 'ORL'
        ];

        for ($i = 1; $i <= 25; $i++) {
            $gender = rand(0, 1);
            $fName = $gender ? $firstNamesF[array_rand($firstNamesF)] : $firstNamesM[array_rand($firstNamesM)];
            $lName = $lastNames[array_rand($lastNames)];
            $specialty = $specialties[array_rand($specialties)];

            \App\Models\MedecinExterne::create([
                'nom' => $lName,
                'prenom' => $fName,
                'email' => strtolower("ex.$fName.$lName." . rand(100, 999) . "@medecin.ci"),
                'telephone' => '+225 01 ' . rand(10, 99) . ' ' . rand(10, 99) . ' ' . rand(10, 99),
                'specialite' => $specialty,
                'numero_ordre' => 'EXT-' . rand(10000, 99999),
                'adresse_cabinet' => 'Abidjan, ' . ['Cocody', 'Plateau', 'Marcory', 'Riviera 2', 'Deux Plateaux'][rand(0, 4)],
                'password' => \Hash::make('password'),
                'statut' => 'actif',
                'is_available' => true,
                'role' => 'doctor',
                'consultation_price' => rand(15, 35) * 1000,
                'base_travel_fee' => 5000,
                'travel_fee_per_km' => 500,
                'max_travel_distance' => 50,
                'latitude' => 5.3 + (rand(0, 100) / 1000), // Autour d'Abidjan
                'longitude' => -4.0 + (rand(0, 100) / 1000),
                'plan_expires_at' => now()->addYear(),
            ]);
        }
    }
}
