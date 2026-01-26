<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MedecinExterne;
use App\Models\ExternalDoctorPrestation;
use Illuminate\Support\Facades\Hash;

class ExternalDoctorSeeder extends Seeder
{
    public function run(): void
    {
        // Clean up existing test doctors to avoid duplicates
        $emails = [
            'abdoulaye.kone@example.com', 'bernadette.traore@example.com', 'cedric.bamba@example.com', 
            'djeneba.ouattara@example.com', 'emmanuel.kouassi@example.com', 'fatou.diallo@example.com', 
            'gerard.toure@example.com', 'helene.soro@example.com', 'ibrahim.diabate@example.com', 
            'juliette.yao@example.com'
        ];
        
        foreach ($emails as $email) {
            $existing = MedecinExterne::where('email', $email)->first();
            if ($existing) {
                $existing->prestations()->delete();
                $existing->recharges()->delete();
                $existing->delete();
            }
        }

        $services = [
            'Cardiologie', 'Cardiologie', 'Cardiologie', 'Cardiologie', // 4 same service
            'Pédiatrie',
            'Chirurgie',
            'Gynécologie',
            'Dermatologie',
            'Ophtalmologie',
            'Radiologie'
        ];

        $firstNames = ['Abdoulaye', 'Bernadette', 'Cédric', 'Djénéba', 'Emmanuel', 'Fatou', 'Gérard', 'Hélène', 'Ibrahim', 'Juliette'];
        $lastNames = ['Koné', 'Traoré', 'Bamba', 'Ouattara', 'Kouassi', 'Diallo', 'Touré', 'Soro', 'Diabaté', 'Yao'];

        foreach ($services as $index => $service) {
            $doctor = MedecinExterne::create([
                'nom' => $lastNames[$index],
                'prenom' => $firstNames[$index],
                'email' => $emails[$index],
                'telephone' => '+22501020304' . $index,
                'specialite' => $service,
                'numero_ordre' => 'ORD-' . (1000 + $index),
                'adresse_cabinet' => 'Abidjan, Cocody Riviera ' . ($index + 1),
                'password' => Hash::make('password'),
                'statut' => 'actif',
                'role' => 'specialist',
                'is_available' => false, // Will be set to true if recharged
                'balance' => 0,
                'plan_expires_at' => null,
            ]);

            // Add prestations for each doctor
            $this->seedPrestations($doctor);

            // Recharge only 8 doctors (index 0 to 7)
            // The last 2 (index 8 and 9) will remain at 0 balance and expired plan
            if ($index < 8) {
                $this->rechargeDoctor($doctor);
            }
        }
    }

    private function rechargeDoctor(MedecinExterne $doctor)
    {
        $amount = 10000;
        $fee = 4000;

        // 1. Create Recharge Record
        $doctor->recharges()->create([
            'amount' => $amount,
            'payment_method' => 'wave',
            'phone_number' => $doctor->telephone,
            'status' => 'completed',
        ]);

        // 2. Update Balance and Plan
        $doctor->balance = $amount - $fee;
        $doctor->plan_expires_at = now()->addDays(30);
        $doctor->is_available = true;
        $doctor->save();

        // 3. Create Transaction Log for Super Admin
        \App\Models\TransactionLog::create([
            'source_type' => 'specialist',
            'source_id' => $doctor->id,
            'amount' => $amount,
            'fee_applied' => $fee,
            'net_income' => $fee,
            'description' => "FRAIS_ACTIVATION: Activation mensuelle spécialiste (via Seeder)",
        ]);
    }

    private function seedPrestations(MedecinExterne $doctor): void
    {
        $specialty = $doctor->specialite;
        
        $prestationsData = [
            'Cardiologie' => [
                ['name' => 'Consultation Cardiologie', 'description' => 'Consultation spécialisée du cœur', 'price' => 20000],
                ['name' => 'Électrocardiogramme (ECG)', 'description' => 'Examen du rythme cardiaque', 'price' => 15000],
                ['name' => 'Échographie Cardiaque', 'description' => 'Imagerie du cœur par ultrasons', 'price' => 45000],
            ],
            'Pédiatrie' => [
                ['name' => 'Consultation Pédiatrie', 'description' => 'Suivi de l\'enfant et du nourrisson', 'price' => 15000],
                ['name' => 'Vaccination', 'description' => 'Administration de vaccins obligatoires', 'price' => 5000],
                ['name' => 'Bilan de croissance', 'description' => 'Évaluation du développement staturo-pondéral', 'price' => 10000],
            ],
            'Chirurgie' => [
                ['name' => 'Consultation Pré-chirurgicale', 'description' => 'Évaluation avant intervention', 'price' => 25000],
                ['name' => 'Petite Chirurgie', 'description' => 'Intervention mineure sous anesthésie locale', 'price' => 50000],
                ['name' => 'Suivi Post-opératoire', 'description' => 'Contrôle après intervention', 'price' => 15000],
            ],
            'Gynécologie' => [
                ['name' => 'Consultation Gynécologie', 'description' => 'Suivi gynécologique classique', 'price' => 20000],
                ['name' => 'Échographie Pelvienne', 'description' => 'Examen des organes génitaux internes', 'price' => 30000],
                ['name' => 'Suivi de Grossesse', 'description' => 'Consultation prénatale', 'price' => 15000],
            ],
            'Dermatologie' => [
                ['name' => 'Consultation Dermatologie', 'description' => 'Examen de la peau', 'price' => 15000],
                ['name' => 'Dermatoscopie', 'description' => 'Examen des grains de beauté', 'price' => 20000],
                ['name' => 'Traitement Verrues', 'description' => 'Cryothérapie ou application topique', 'price' => 10000],
            ],
            'Ophtalmologie' => [
                ['name' => 'Examen de Vue', 'description' => 'Contrôle de l\'acuité visuelle', 'price' => 15000],
                ['name' => 'Fond d\'œil', 'description' => 'Examen de la rétine', 'price' => 25000],
                ['name' => 'Mesure Tension Oculaire', 'description' => 'Dépistage du glaucome', 'price' => 10000],
            ],
            'Radiologie' => [
                ['name' => 'Échographie Abdominale', 'description' => 'Imagerie de l\'abdomen', 'price' => 25000],
                ['name' => 'Radiographie Thorax', 'description' => 'Cliché pulmonaire', 'price' => 15000],
                ['name' => 'Interprétation Scanner', 'description' => 'Lecture et compte-rendu d\'imagerie', 'price' => 20000],
            ],
        ];

        $data = $prestationsData[$specialty] ?? [
            ['name' => 'Consultation Spécialisée', 'description' => 'Consultation expert', 'price' => 15000],
            ['name' => 'Examen Complémentaire', 'description' => 'Examen de suivi', 'price' => 10000],
        ];

        foreach ($data as $item) {
            ExternalDoctorPrestation::create([
                'medecin_externe_id' => $doctor->id,
                'name' => $item['name'],
                'description' => $item['description'],
                'price' => $item['price'],
                'commission_percentage' => 10.00, // Default commission
                'is_active' => true,
            ]);
        }
    }
}
