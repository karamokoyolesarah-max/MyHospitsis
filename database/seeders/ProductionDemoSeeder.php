<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\{Hospital, Service, User, Patient, MedecinExterne, ExternalDoctorPrestation, Room};

/**
 * Seeder de démonstration pour la production.
 * 
 * Crée des comptes test pour :
 * - 1 Hôpital avec services
 * - Personnel hospi (admin, médecin interne, infirmier, caissier, secrétaire)
 * - 2 Patients avec compte portail (login/password)
 * - 2 Médecins externes (spécialistes)
 * 
 * ⚠️  Ce seeder utilise updateOrCreate pour ne PAS dupliquer les données.
 * 
 * COMMANDE : php artisan db:seed --class=ProductionDemoSeeder
 */
class ProductionDemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🏥 Création de l\'hôpital de démo...');
        
        // =============================================
        // 1. HÔPITAL
        // =============================================
        $hospital = Hospital::updateOrCreate(
            ['slug' => 'clinique-demo'],
            [
                'name' => 'Clinique Démo HospitSIS',
                'address' => 'Abidjan, Cocody Angré',
                'is_active' => true,
            ]
        );

        // =============================================
        // 2. SERVICES
        // =============================================
        $servicesData = [
            ['name' => 'Urgences',     'code' => 'URG-DEMO',  'description' => 'Service des urgences',          'consultation_price' => 15000],
            ['name' => 'Cardiologie',  'code' => 'CARD-DEMO', 'description' => 'Service de cardiologie',        'consultation_price' => 25000],
            ['name' => 'Pédiatrie',    'code' => 'PED-DEMO',  'description' => 'Service de pédiatrie',          'consultation_price' => 20000],
            ['name' => 'Chirurgie',    'code' => 'CHIR-DEMO', 'description' => 'Service de chirurgie générale', 'consultation_price' => 30000],
            ['name' => 'Maternité',    'code' => 'MAT-DEMO',  'description' => 'Service de maternité',          'consultation_price' => 18000],
        ];

        $services = [];
        foreach ($servicesData as $s) {
            $services[$s['name']] = Service::updateOrCreate(
                ['code' => $s['code'], 'hospital_id' => $hospital->id],
                array_merge($s, ['hospital_id' => $hospital->id, 'is_active' => true])
            );
        }

        $this->command->info('✅ Hôpital + 5 services créés');

        // =============================================
        // 3. PERSONNEL HOSPITALIER (Users)
        // =============================================
        $staffAccounts = [
            [
                'name'     => 'Admin Démo',
                'email'    => 'admin@demo.hospitis.ci',
                'role'     => 'admin',
                'service'  => null,
            ],
            [
                'name'     => 'Dr. Traoré Moussa',
                'email'    => 'medecin@demo.hospitis.ci',
                'role'     => 'doctor',
                'service'  => 'Cardiologie',
            ],
            [
                'name'     => 'Dr. Koné Aminata',
                'email'    => 'interne@demo.hospitis.ci',
                'role'     => 'internal_doctor',
                'service'  => 'Urgences',
            ],
            [
                'name'     => 'Infirmier Diallo',
                'email'    => 'infirmier@demo.hospitis.ci',
                'role'     => 'nurse',
                'service'  => 'Cardiologie',
            ],
            [
                'name'     => 'Caissier Bamba',
                'email'    => 'caissier@demo.hospitis.ci',
                'role'     => 'cashier',
                'service'  => null,
            ],
            [
                'name'     => 'Secrétaire Yao',
                'email'    => 'secretaire@demo.hospitis.ci',
                'role'     => 'secretary',
                'service'  => null,
            ],
            [
                'name'     => 'Responsable Admin Kouadio',
                'email'    => 'responsable@demo.hospitis.ci',
                'role'     => 'administrative',
                'service'  => null,
            ],
        ];

        foreach ($staffAccounts as $staff) {
            User::updateOrCreate(
                ['email' => $staff['email']],
                [
                    'hospital_id' => $hospital->id,
                    'name'        => $staff['name'],
                    'password'    => Hash::make('password'),
                    'role'        => $staff['role'],
                    'service_id'  => $staff['service'] ? ($services[$staff['service']]->id ?? null) : null,
                    'is_active'   => true,
                ]
            );
        }

        $this->command->info('✅ 7 comptes personnel hospitalier créés');

        // =============================================
        // 4. PATIENTS (Portail Patient)
        // =============================================
        $patient1 = Patient::updateOrCreate(
            ['email' => 'patient@demo.hospitis.ci'],
            [
                'hospital_id'  => $hospital->id,
                'ipu'          => 'PAT2026-DEMO1',
                'name'         => 'Kouassi',
                'first_name'   => 'Ama',
                'dob'          => '1990-03-15',
                'gender'       => 'Femme',
                'phone'        => '+225 07 00 00 01',
                'address'      => 'Cocody Angré, Abidjan',
                'city'         => 'Abidjan',
                'blood_group'  => 'O+',
                'is_active'    => true,
                'password'     => Hash::make('password'),
            ]
        );

        $patient2 = Patient::updateOrCreate(
            ['email' => 'patient2@demo.hospitis.ci'],
            [
                'hospital_id'  => $hospital->id,
                'ipu'          => 'PAT2026-DEMO2',
                'name'         => 'Traoré',
                'first_name'   => 'Ibrahim',
                'dob'          => '1985-07-22',
                'gender'       => 'Homme',
                'phone'        => '+225 07 00 00 02',
                'address'      => 'Plateau, Abidjan',
                'city'         => 'Abidjan',
                'blood_group'  => 'A+',
                'is_active'    => true,
                'password'     => Hash::make('password'),
            ]
        );

        $this->command->info('✅ 2 comptes patients créés');

        // =============================================
        // 5. MÉDECINS EXTERNES (Spécialistes)
        // =============================================
        $externals = [
            [
                'nom'             => 'Bakayoko',
                'prenom'          => 'Seydou',
                'email'           => 'specialiste@demo.hospitis.ci',
                'telephone'       => '+225 05 00 00 01',
                'specialite'      => 'Cardiologie',
                'numero_ordre'    => 'ORD-DEMO-001',
                'adresse_cabinet' => 'Cocody Riviera 3, Abidjan',
                'consultation_price' => 25000,
                'prestations'     => [
                    ['name' => 'Consultation Cardiologie', 'description' => 'Consultation spécialisée du cœur', 'price' => 20000],
                    ['name' => 'Électrocardiogramme (ECG)', 'description' => 'Examen du rythme cardiaque', 'price' => 15000],
                    ['name' => 'Échographie Cardiaque', 'description' => 'Imagerie du cœur par ultrasons', 'price' => 45000],
                ],
            ],
            [
                'nom'             => 'Soro',
                'prenom'          => 'Mariam',
                'email'           => 'specialiste2@demo.hospitis.ci',
                'telephone'       => '+225 05 00 00 02',
                'specialite'      => 'Pédiatrie',
                'numero_ordre'    => 'ORD-DEMO-002',
                'adresse_cabinet' => 'Marcory Zone 4, Abidjan',
                'consultation_price' => 20000,
                'prestations'     => [
                    ['name' => 'Consultation Pédiatrie', 'description' => 'Suivi de l\'enfant et du nourrisson', 'price' => 15000],
                    ['name' => 'Vaccination', 'description' => 'Administration de vaccins', 'price' => 5000],
                    ['name' => 'Bilan de croissance', 'description' => 'Évaluation du développement', 'price' => 10000],
                ],
            ],
        ];

        foreach ($externals as $ext) {
            $prestations = $ext['prestations'];
            unset($ext['prestations']);

            $doctor = MedecinExterne::updateOrCreate(
                ['email' => $ext['email']],
                array_merge($ext, [
                    'password'        => Hash::make('password'),
                    'statut'          => 'actif',
                    'role'            => 'specialist',
                    'is_available'    => true,
                    'balance'         => 6000,
                    'plan_expires_at' => now()->addDays(30),
                    'latitude'        => 5.3600,
                    'longitude'       => -3.9800,
                    'travel_fee_type' => 'per_km',
                    'base_travel_fee' => 2000,
                    'travel_fee_per_km' => 500,
                    'max_travel_distance' => 30,
                ])
            );

            // Ajouter les prestations
            foreach ($prestations as $prest) {
                ExternalDoctorPrestation::updateOrCreate(
                    ['medecin_externe_id' => $doctor->id, 'name' => $prest['name']],
                    array_merge($prest, [
                        'medecin_externe_id' => $doctor->id,
                        'commission_percentage' => 10.00,
                        'is_active' => true,
                    ])
                );
            }
        }

        $this->command->info('✅ 2 médecins externes (spécialistes) créés');

        // =============================================
        // 6. CHAMBRES
        // =============================================
        foreach ($services as $name => $service) {
            Room::updateOrCreate(
                ['room_number' => 'DEMO-' . $service->code],
                [
                    'hospital_id'  => $hospital->id,
                    'bed_capacity' => 3,
                    'service_id'   => $service->id,
                    'status'       => 'available',
                    'type'         => 'standard',
                    'is_active'    => true,
                ]
            );
        }

        $this->command->info('✅ Chambres créées');

        // =============================================
        // RÉCAPITULATIF
        // =============================================
        $this->command->newLine();
        $this->command->info('╔══════════════════════════════════════════════════╗');
        $this->command->info('║      🎉 COMPTES DE DÉMONSTRATION CRÉÉS !        ║');
        $this->command->info('╠══════════════════════════════════════════════════╣');
        $this->command->info('║  Mot de passe universel : password              ║');
        $this->command->info('╠══════════════════════════════════════════════════╣');
        $this->command->info('║  👤 PATIENTS (Portail /portal/login)            ║');
        $this->command->info('║    patient@demo.hospitis.ci                     ║');
        $this->command->info('║    patient2@demo.hospitis.ci                    ║');
        $this->command->info('╠══════════════════════════════════════════════════╣');
        $this->command->info('║  🩺 MÉDECINS EXTERNES (/medecin/externe/login)  ║');
        $this->command->info('║    specialiste@demo.hospitis.ci                 ║');
        $this->command->info('║    specialiste2@demo.hospitis.ci                ║');
        $this->command->info('╠══════════════════════════════════════════════════╣');
        $this->command->info('║  🏥 PERSONNEL HOSPI (/login)                    ║');
        $this->command->info('║    admin@demo.hospitis.ci          (Admin)      ║');
        $this->command->info('║    medecin@demo.hospitis.ci        (Médecin)    ║');
        $this->command->info('║    interne@demo.hospitis.ci        (Interne)    ║');
        $this->command->info('║    infirmier@demo.hospitis.ci      (Infirmier)  ║');
        $this->command->info('║    caissier@demo.hospitis.ci       (Caissier)   ║');
        $this->command->info('║    secretaire@demo.hospitis.ci     (Secrétaire) ║');
        $this->command->info('║    responsable@demo.hospitis.ci    (Admin RH)   ║');
        $this->command->info('╚══════════════════════════════════════════════════╝');
    }
}
