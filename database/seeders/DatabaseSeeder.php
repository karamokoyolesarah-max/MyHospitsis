<?php

namespace Database\Seeders;

use App\Models\Appointment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\{Hash, DB};
use App\Models\{Service, User, Patient, Room, Hospital, PatientVital};
use Database\Seeders\SuperAdminSeeder;

class DatabaseSeeder extends Seeder

{ 
    public function run(): void
{
    DB::statement('SET FOREIGN_KEY_CHECKS=0;');

    Hospital::truncate();
    Service::truncate();
    User::truncate();
    Patient::truncate();
    Room::truncate();
    Appointment::truncate();
    PatientVital::truncate();

    DB::statement('SET FOREIGN_KEY_CHECKS=1;');

    // Seed Super Admin
    $this->call(SuperAdminSeeder::class);

    // Seed Subscription Plans
    $this->call(SubscriptionPlanSeeder::class);

    // --- 1. CRÉATION DES HÔPITAUX ---
    $hospital1 = Hospital::create([
        'name' => 'Centre Hospitalier HospisIS',
        'slug' => 'hospisis-ci',
        'address' => 'Abidjan, Côte d\'Ivoire',
    ]);
    
    $this->seedAllData($hospital1->id, 'hospisis.ci', 'HospisIS');
    $this->command->info('✅ Données Hôpital 1 (HospisIS) terminées');

    $hospital2 = Hospital::create([
        'name' => 'Clinique Médicale Saint-Jean',
        'slug' => 'saint-jean-ci',
        'address' => 'Abidjan, Plateau',
        'logo' => 'logos/saint-jean-logo.svg',
        'is_active' => true,
    ]);

    $this->seedAllData($hospital2->id, 'saintjean.ci', 'Saint-Jean');
    $this->command->info('✅ Données Hôpital 2 (Saint-Jean) terminées');

    $hospital3 = Hospital::create([
        'name' => 'Clinique Sarah',
        'slug' => 'sarah-ci',
        'address' => 'Abidjan, Yopougon',
        'logo' => 'logos/sarah-logo.svg',
        'is_active' => true,
    ]);

    $this->seedAllData($hospital3->id, 'sarah.ci', 'Sarah');
    $this->command->info('✅ Données Hôpital 3 (Sarah) terminées');

    // --- 2. CRÉATION DU MÉDECIN (Cardiologie) ---
    $doctor = User::updateOrCreate(
        ['email' => 'doctor@saintjean.ci'],
        [
            'name' => 'Dr. Kouamé Jean',
            'password' => Hash::make('password'),
            'role' => 'doctor',
            'service_id' => 7, // Cardiologie
            'hospital_id' => $hospital2->id,
        ]
    );

    // --- 3. CRÉATION DE L'INFIRMIER (Cardiologie) ---
    $nurseCardio = User::updateOrCreate(
        ['email' => 'infirmier.cardio@saintjean.ci'],
        [
            'name' => 'Infirmier Diallo (Cardio)',
            'password' => Hash::make('password'),
            'role' => 'nurse',
            'service_id' => 7,
            'hospital_id' => $hospital2->id,
        ]
    );

    // --- 4. CRÉATION DU CAISSIER ---
    $cashier = User::updateOrCreate(
        ['email' => 'cashier@saintjean.ci'],
        [
            'name' => 'Caissier Dupont (Saint-Jean)',
            'password' => Hash::make('password'),
            'role' => 'cashier',
            'hospital_id' => $hospital2->id,
            'is_active' => true,
        ]
    );

    // --- 1. CRÉATION DU NOUVEAU PATIENT (Kouadio Konan) ---
    $newPatient = Patient::updateOrCreate(
        ['ipu' => 'IPU-2026-TEST'], // IPU unique
        [
            'hospital_id' => $hospital2->id, // Clinique Saint-Jean
            'name' => 'Kouadio Konan',
            'first_name' => 'Konan',
            'dob' => '1990-01-01',
            'gender' => 'Homme',
            'phone' => '+225 07 01 23 45',
            'is_active' => true,
        ]
    );

    // --- 3. CRÉATION DU RENDEZ-VOUS POUR AUJOURD'HUI ---
    // Note : Ta capture d'écran montre la date du 08/01/2026
    Appointment::create([
        'patient_id' => $newPatient->id,
        'doctor_id' => $doctor->id,
        'appointment_datetime' => '2026-01-08 14:00:00', // Heure fixée pour aujourd'hui
        'service_id' => 7,
        'hospital_id' => $hospital2->id,
        'status' => 'scheduled',
        'reason' => 'Test de visibilité médecin',
        'type' => 'consultation',
    ]);

    $this->command->info('✅ Patient Kouadio Konan ajouté pour le Dr. Kouamé Jean le 08/01/2026');

    // --- 5. CRÉATION DU PATIENT AMA ---
    $patientAma = Patient::updateOrCreate(
        ['id' => 12],
        [
            'name' => 'Kouassi Ama',
            'gender' => 'Femme',
            'dob' => '1995-05-15',
            'hospital_id' => $hospital2->id,
        ]
    );

    // --- 5. CRÉATION DU PATIENT Ange ---
    $patientAnge = Patient::updateOrCreate(
        ['id' => 13],
        [
            'name' => 'Kouassi Ange',
            'gender' => 'Femme',
            'dob' => '1995-05-15',
            'hospital_id' => $hospital2->id,
        ]
    );

    // --- 5. RENDEZ-VOUS POUR AMA ---
    Appointment::updateOrCreate(
        [
            'patient_id' => $patientAma->id,
            'doctor_id' => $doctor->id,
            'appointment_datetime' => '2025-12-22 10:30:00', 
        ],
        [
            'service_id' => 7,
            'hospital_id' => $hospital2->id,
            'status' => 'scheduled', // Valeur autorisée par votre ENUM
            'reason' => 'Consultation Cardiologie - Suivi Ama',
            'type' => 'consultation',
        ]
    );

    $this->command->info('🚀 Base de données initialisée ! Ama est prête.');

    // --- 5. RENDEZ-VOUS POUR Ange ---
    Appointment::updateOrCreate(
        [
            'patient_id' => $patientAnge->id,
            'doctor_id' => $doctor->id,
            'appointment_datetime' => 'now', 
        ],
        [
            'service_id' => 7,
            'hospital_id' => $hospital2->id,
            'status' => 'scheduled', // Valeur autorisée par votre ENUM
            'reason' => 'Consultation Cardiologie - Suivi Ama',
            'type' => 'consultation',
        ]
    );

    $this->command->info('🚀 Base de données initialisée ! Ange est prête.');

    // --- 6. CRÉATION DU DOSSIER DE CONSTANTES POUR Ange ---
    PatientVital::create([
        'patient_name' => 'Kouassi Ange',
        'patient_ipu' => 'PAT2025001', // Assuming an IPU for Ange
        'temperature' => 37.2,
        'pulse' => 72,
        'blood_pressure' => '120/80',
        'weight' => 65.5,
        'urgency' => 'normal',
        'reason' => 'Consultation de routine',
        'notes' => 'Patient en bonne santé générale',
        'user_id' => $nurseCardio->id,
        'hospital_id' => $hospital2->id,
        'service_id' => 7, // Cardiologie
    ]);

    $this->command->info('✅ Dossier de constantes créé pour Ange.');

    // Seed Laboratory Services (after hospitals and basic services)
    $this->call(LaboratoryServicesSeeder::class);
}

/**
 * Fonction helper pour les données de base par hôpital
 */
 

private function seedAllData(int $hId, string $domain, string $suffix): void
    {
        $this->seedServices($hId);
        $this->seedUsers($hId, $domain, $suffix);
        $this->seedPatients($hId, $suffix);
        $this->seedRooms($hId);
    }

     
    private function seedServices(int $hId): void
    {
        // On ajoute l'ID de l'hôpital au code pour éviter le conflit unique
        // Exemple : URG-1 et URG-2
        $services = [
            ['hospital_id' => $hId, 'name' => 'Urgences', 'code' => 'URG-' . $hId, 'description' => 'Service des urgences', 'consultation_price' => 15000, 'is_active' => true],
            ['hospital_id' => $hId, 'name' => 'Cardiologie', 'code' => 'CARD-' . $hId, 'description' => 'Service de cardiologie', 'consultation_price' => 25000, 'is_active' => true],
            ['hospital_id' => $hId, 'name' => 'Pédiatrie', 'code' => 'PED-' . $hId, 'description' => 'Service de pédiatrie', 'consultation_price' => 20000, 'is_active' => true],
            ['hospital_id' => $hId, 'name' => 'Chirurgie', 'code' => 'CHIR-' . $hId, 'description' => 'Service de chirurgie générale', 'consultation_price' => 30000, 'is_active' => true],
            ['hospital_id' => $hId, 'name' => 'Maternité', 'code' => 'MAT-' . $hId, 'description' => 'Service de maternité', 'consultation_price' => 18000, 'is_active' => true],
        ];

        foreach ($services as $service) {
            Service::create($service);
        }
    }
         
    

    private function seedUsers(int $hId, string $domain, string $suffix): void
    {
        // Admin Principal
        User::create([
            'hospital_id' => $hId,
            'name' => "Admin Système $suffix",
            'email' => "admin@$domain",
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        // Responsable Administratif
        User::create([
            'hospital_id' => $hId,
            'name' => "Sophie Martin $suffix",
            'email' => "admin.responsable@$domain",
            'password' => Hash::make('password'),
            'role' => 'administrative',
            'is_active' => true,
        ]);

       
    // ... (Admin et Responsable déjà là)

    // Médecin INTERNE (Travaille sur place)
    User::create([
        'hospital_id' => $hId,
        'name' => "Dr. Interne Traoré ($suffix)",
        'email' => "interne@$domain",
        'password' => Hash::make('password'),
        'role' => 'internal_doctor',
        'service_id' => Service::where('hospital_id', $hId)->first()->id,
        'is_active' => true,
    ]);

    // Médecin EXTERNE (Cabinet privé / Consultant)
    User::create([
        'hospital_id' => $hId,
        'name' => "Dr. Externe Bakayoko ($suffix)",
        'email' => "externe@$domain",
        'password' => Hash::make('password'),
        'role' => 'external_doctor',
        'is_active' => true,
    ]);

    // Infirmier
    User::create([
        'hospital_id' => $hId,
        'name' => "Infirmier Koffi ($suffix)",
        'email' => "nurse@$domain",
        'password' => Hash::make('password'),
        'role' => 'nurse',
        'service_id' => Service::where('hospital_id', $hId)->first()->id,
        'is_active' => true,
    ]);
}

    

    private function seedPatients(int $hId, string $suffix): void
    {
        $firstNames = ['Kofi', 'Ama', 'Koffi', 'Adjoua', 'Kouadio'];
        $lastNames = ['Kouassi', 'Yao', 'Koné', 'Traoré', 'Bamba'];

        for ($i = 0; $i < 10; $i++) { // 10 patients par hôpital pour l'exemple
            Patient::create([
                'hospital_id' => $hId,
                'ipu' => 'IPU-'.$hId.rand(100000, 999999),
                'first_name' => $firstNames[array_rand($firstNames)],
                'name' => $lastNames[array_rand($lastNames)] . " ($suffix)",
                'dob' => now()->subYears(rand(1, 80)),
                'gender' => ['Homme', 'Femme'][rand(0, 1)],
                'address' => 'Abidjan',
                'city' => 'Abidjan',
                'phone' => '+225 07 ' . rand(10, 99) . ' 00 00',
                'is_active' => true,
            ]);
        }
    }

    private function seedRooms(int $hId): void
    {
        $services = Service::where('hospital_id', $hId)->get();
        foreach ($services as $service) {
            Room::create([
                'hospital_id' => $hId,
                'room_number' => $service->code . '-' . rand(100, 999),
                'bed_capacity' => rand(1, 4),
                'service_id' => $service->id,
                'status' => 'available',
                'type' => 'standard',
                'is_active' => true,
            ]);
        }
    }
  
    

}