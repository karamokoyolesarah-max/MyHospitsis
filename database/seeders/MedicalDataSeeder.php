<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MedicalDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \App\Models\Service::truncate();
        \App\Models\Prestation::truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $hospitals = \App\Models\Hospital::all();
        if ($hospitals->isEmpty()) {
            $hospital = \App\Models\Hospital::create([
                'name' => 'Hôpital Central de Référence',
                'slug' => 'hopital-central',
                'address' => 'Abidjan, Cocody Riviera',
                'is_active' => true,
            ]);
            $hospitals = collect([$hospital]);
        }

        $standardServices = [
            'Cardiologie' => [
                'code' => 'CARD',
                'prestations' => [
                    ['name' => 'Consultation Cardiologie', 'price' => 25000, 'category' => 'consultation'],
                    ['name' => 'Électrocardiogramme (ECG)', 'price' => 15000, 'category' => 'examen'],
                    ['name' => 'Échographie Cardiaque', 'price' => 45000, 'category' => 'examen'],
                    ['name' => 'Holter Tensionnel (MAPA)', 'price' => 30000, 'category' => 'examen'],
                    ['name' => 'Épreuve d\'effort', 'price' => 50000, 'category' => 'examen'],
                    ['name' => 'Doppler Carotidien', 'price' => 35000, 'category' => 'examen'],
                    ['name' => 'Contrôle Stimulateur Cardiaque', 'price' => 40000, 'category' => 'soins'],
                ]
            ],
            'Radiologie & Imagerie' => [
                'code' => 'RAD',
                'prestations' => [
                    ['name' => 'Scanner Cérébral (TDM)', 'price' => 85000, 'category' => 'examen'],
                    ['name' => 'IRM Lombaire', 'price' => 150000, 'category' => 'examen'],
                    ['name' => 'Radiographie du Thorax', 'price' => 15000, 'category' => 'examen'],
                    ['name' => 'Échographie Abdomino-pelvienne', 'price' => 20000, 'category' => 'examen'],
                    ['name' => 'Mammographie Bilatérale', 'price' => 35000, 'category' => 'examen'],
                    ['name' => 'Panoramique Dentaire', 'price' => 15000, 'category' => 'examen'],
                    ['name' => 'Ostéodensitométrie', 'price' => 45000, 'category' => 'examen'],
                    ['name' => 'Angioscanner', 'price' => 120000, 'category' => 'examen'],
                ]
            ],
            'Laboratoire d\'Analyses' => [
                'code' => 'LABO',
                'prestations' => [
                    ['name' => 'Bilan Sanguin (NFS/Plaquettes)', 'price' => 8000, 'category' => 'examen'],
                    ['name' => 'Glycémie à jeun', 'price' => 3000, 'category' => 'examen'],
                    ['name' => 'Test Paludisme (TDR)', 'price' => 2000, 'category' => 'examen'],
                    ['name' => 'Goutte Épaisse (GE)', 'price' => 3500, 'category' => 'examen'],
                    ['name' => 'Bilan Lipidique (Cholestérol)', 'price' => 12000, 'category' => 'examen'],
                    ['name' => 'Créatinémie (Fonction rénale)', 'price' => 5000, 'category' => 'examen'],
                    ['name' => 'Hémoglobine Glyquée (HbA1c)', 'price' => 15000, 'category' => 'examen'],
                    ['name' => 'Test VIH', 'price' => 0, 'category' => 'examen'],
                    ['name' => 'ECBU (Urines)', 'price' => 10000, 'category' => 'examen'],
                    ['name' => 'Coproculture', 'price' => 12000, 'category' => 'examen'],
                ]
            ],
            'Pédiatrie' => [
                'code' => 'PED',
                'prestations' => [
                    ['name' => 'Consultation Pédiatrique', 'price' => 15000, 'category' => 'consultation'],
                    ['name' => 'Vaccination BCG / Polio', 'price' => 5000, 'category' => 'soins'],
                    ['name' => 'Vaccination Pentavalent', 'price' => 10000, 'category' => 'soins'],
                    ['name' => 'Suivi Croissance & Nutrition', 'price' => 12000, 'category' => 'consultation'],
                    ['name' => 'Nébulisation / Aérosolhérapie', 'price' => 8000, 'category' => 'soins'],
                    ['name' => 'Test de Denver (Développement)', 'price' => 20000, 'category' => 'examen'],
                    ['name' => 'Circoncision Médicale', 'price' => 35000, 'category' => 'soins'],
                ]
            ],
            'Gynécologie & Obstétrique' => [
                'code' => 'GYNE',
                'prestations' => [
                    ['name' => 'Consultation Gynécologique', 'price' => 20000, 'category' => 'consultation'],
                    ['name' => 'Suivi de Grossesse (CPN)', 'price' => 15000, 'category' => 'consultation'],
                    ['name' => 'Échographie Obstétricale', 'price' => 25000, 'category' => 'examen'],
                    ['name' => 'Accouchement Voie Basse', 'price' => 150000, 'category' => 'soins'],
                    ['name' => 'Césarienne Programmée', 'price' => 450000, 'category' => 'soins'],
                    ['name' => 'Frottis Cervico-Vaginal', 'price' => 18000, 'category' => 'examen'],
                    ['name' => 'Pose / Retrait Implant', 'price' => 25000, 'category' => 'soins'],
                ]
            ],
            'Médecine Générale' => [
                'code' => 'GEN',
                'prestations' => [
                    ['name' => 'Consultation Médecine Générale', 'price' => 10000, 'category' => 'consultation'],
                    ['name' => 'Certificat de visite médicale', 'price' => 7500, 'category' => 'soins'],
                    ['name' => 'Prise de Constantes (TA, Pouls)', 'price' => 2000, 'category' => 'soins'],
                    ['name' => 'Lavage d\'oreille', 'price' => 15000, 'category' => 'soins'],
                    ['name' => 'Suture de plaie simple', 'price' => 25000, 'category' => 'soins'],
                ]
            ],
            'Dermatologie' => [
                'code' => 'DERM',
                'prestations' => [
                    ['name' => 'Consultation Dermatologie', 'price' => 20000, 'category' => 'consultation'],
                    ['name' => 'Dermatoscopie', 'price' => 15000, 'category' => 'examen'],
                    ['name' => 'Séance de Cryothérapie', 'price' => 25000, 'category' => 'soins'],
                    ['name' => 'Biopsie Cutanée', 'price' => 45000, 'category' => 'soins'],
                    ['name' => 'Traitement Acné Sévère', 'price' => 30000, 'category' => 'soins'],
                ]
            ],
            'Ophtalmologie' => [
                'code' => 'OPHT',
                'prestations' => [
                    ['name' => 'Consultation Ophtalmo', 'price' => 18000, 'category' => 'consultation'],
                    ['name' => 'Examen Fond d\'œil', 'price' => 12000, 'category' => 'examen'],
                    ['name' => 'Réfraction (Lunettes)', 'price' => 5000, 'category' => 'examen'],
                    ['name' => 'OCT Maculaire', 'price' => 45000, 'category' => 'examen'],
                    ['name' => 'Chirurgie Cataracte (1 œil)', 'price' => 350000, 'category' => 'soins'],
                ]
            ],
            'Chirurgie Dentaire' => [
                'code' => 'DENT',
                'prestations' => [
                    ['name' => 'Consultation Dentaire', 'price' => 10000, 'category' => 'consultation'],
                    ['name' => 'Détartrage complet', 'price' => 25000, 'category' => 'soins'],
                    ['name' => 'Extraction dentaire simple', 'price' => 20000, 'category' => 'soins'],
                    ['name' => 'Obturation (Plombage Composite)', 'price' => 35000, 'category' => 'soins'],
                    ['name' => 'Traitement de canal', 'price' => 50000, 'category' => 'soins'],
                    ['name' => 'Prothèse dentaire mobile', 'price' => 120000, 'category' => 'soins'],
                ]
            ],
            'Gastro-entérologie' => [
                'code' => 'GASTRO',
                'prestations' => [
                    ['name' => 'Consultation Gastro', 'price' => 20000, 'category' => 'consultation'],
                    ['name' => 'Fibroscopie Oeso-gastrique', 'price' => 55000, 'category' => 'examen'],
                    ['name' => 'Colonoscopie', 'price' => 110000, 'category' => 'examen'],
                    ['name' => 'Échographie Abdominale', 'price' => 20000, 'category' => 'examen'],
                ]
            ],
            'Urgences 24h/24' => [
                'code' => 'URG',
                'prestations' => [
                    ['name' => 'Consultation d\'Urgence', 'price' => 25000, 'category' => 'consultation'],
                    ['name' => 'Mise en Observation / Heure', 'price' => 5000, 'category' => 'soins'],
                    ['name' => 'Perfusion simple', 'price' => 12000, 'category' => 'soins'],
                    ['name' => 'Oxygénothérapie / Heure', 'price' => 7000, 'category' => 'soins'],
                    ['name' => 'Nébulisation d\'urgence', 'price' => 10000, 'category' => 'soins'],
                    ['name' => 'Transport Ambulance Proximité', 'price' => 35000, 'category' => 'soins'],
                ]
            ],
            'Kinésithérapie' => [
                'code' => 'KINE',
                'prestations' => [
                    ['name' => 'Bilan Kiné Initial', 'price' => 15000, 'category' => 'consultation'],
                    ['name' => 'Séance Rééducation Motrice', 'price' => 10000, 'category' => 'soins'],
                    ['name' => 'Massage Thérapeutique', 'price' => 15000, 'category' => 'soins'],
                    ['name' => 'Rééducation Respiratoire', 'price' => 12000, 'category' => 'soins'],
                ]
            ],
            'Neurologie' => [
                'code' => 'NEURO',
                'prestations' => [
                    ['name' => 'Consultation Neurologie', 'price' => 25000, 'category' => 'consultation'],
                    ['name' => 'Électro-encéphalogramme (EEG)', 'price' => 45000, 'category' => 'examen'],
                    ['name' => 'EMG (Électromyogramme)', 'price' => 60000, 'category' => 'examen'],
                ]
            ],
            'Urologie' => [
                'code' => 'UROL',
                'prestations' => [
                    ['name' => 'Consultation Urologie', 'price' => 20000, 'category' => 'consultation'],
                    ['name' => 'Échographie Prostatique', 'price' => 20000, 'category' => 'examen'],
                    ['name' => 'Pose de sonde urinaire', 'price' => 15000, 'category' => 'soins'],
                ]
            ],
            'ORL' => [
                'code' => 'ORL',
                'prestations' => [
                    ['name' => 'Consultation ORL', 'price' => 20000, 'category' => 'consultation'],
                    ['name' => 'Audiométrie', 'price' => 25000, 'category' => 'examen'],
                    ['name' => 'Fibroscopie Nasale', 'price' => 30000, 'category' => 'examen'],
                ]
            ],
            'Chirurgie Générale' => [
                'code' => 'CHIR',
                'prestations' => [
                    ['name' => 'Consultation Chirurgicale', 'price' => 25000, 'category' => 'consultation'],
                    ['name' => 'Appendicectomie', 'price' => 250000, 'category' => 'soins'],
                    ['name' => 'Cure de hernie inguinale', 'price' => 180000, 'category' => 'soins'],
                ]
            ],
            'Psychiatrie' => [
                'code' => 'PSY',
                'prestations' => [
                    ['name' => 'Consultation Psychiatrique', 'price' => 30000, 'category' => 'consultation'],
                    ['name' => 'Séance de Psychothérapie', 'price' => 25000, 'category' => 'soins'],
                    ['name' => 'Bilan Psychologique', 'price' => 40000, 'category' => 'examen'],
                ]
            ],
            'Néphrologie' => [
                'code' => 'NEPH',
                'prestations' => [
                    ['name' => 'Consultation Néphrologie', 'price' => 25000, 'category' => 'consultation'],
                    ['name' => 'Séance de Dialyse', 'price' => 60000, 'category' => 'soins'],
                    ['name' => 'Échographie Rénale', 'price' => 20000, 'category' => 'examen'],
                ]
            ],
            'Rhumatologie' => [
                'code' => 'RHUM',
                'prestations' => [
                    ['name' => 'Consultation Rhumatologie', 'price' => 25000, 'category' => 'consultation'],
                    ['name' => 'Infiltration Articulaire', 'price' => 35000, 'category' => 'soins'],
                    ['name' => 'Radiographie Articulaire', 'price' => 15000, 'category' => 'examen'],
                ]
            ],
        ];

        $prestationCounter = 101;
        foreach ($hospitals as $hospital) {
            foreach ($standardServices as $serviceName => $data) {
                $service = \App\Models\Service::create([
                    'hospital_id' => $hospital->id,
                    'name' => $serviceName,
                    'code' => $data['code'] . '-' . $hospital->id,
                    'description' => 'Service de ' . $serviceName . ' - ' . $hospital->name,
                    'consultation_price' => $data['prestations'][0]['price'] ?? 15000,
                    'is_active' => true,
                ]);

                foreach ($data['prestations'] as $prestatData) {
                    \App\Models\Prestation::create([
                        'hospital_id' => $hospital->id,
                        'service_id' => $service->id,
                        'name' => $prestatData['name'],
                        'price' => $prestatData['price'],
                        'category' => $prestatData['category'] ?? 'examen',
                        'code' => strtoupper($data['code']) . '-' . ($prestationCounter++),
                        'is_active' => true,
                        'description' => $prestatData['name'] . ' hospitalier',
                    ]);
                }
            }
        }
    }
}
