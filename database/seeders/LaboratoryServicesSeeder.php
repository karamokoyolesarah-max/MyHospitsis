<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Hospital;
use App\Models\Service;
use App\Models\Prestation;
use Illuminate\Support\Str;

class LaboratoryServicesSeeder extends Seeder
{
    public function run(): void
    {
        $hospitals = Hospital::all();

        foreach ($hospitals as $hospital) {
            $this->seedLaboratoryForHospital($hospital);
        }
    }

    private function seedLaboratoryForHospital(Hospital $hospital): void
    {
        $labServices = [
            [
                'name' => 'Biochimie Clinique',
                'code' => 'LAB-BIO',
                'description' => 'Analyses biochimiques du sang et des liquides biologiques.',
                'color' => '#3b82f6', // Blue
                'icon' => 'flask-conical',
                'prestations' => [
                    ['name' => 'Glycémie à jeun', 'price' => 2000],
                    ['name' => 'Bilan Lipidique (Cholestérol/TG)', 'price' => 8000],
                    ['name' => 'Urée Sanguine', 'price' => 2500],
                    ['name' => 'Créatininémie', 'price' => 3500],
                    ['name' => 'Acide Urique', 'price' => 3000],
                    ['name' => 'Transaminases (ASAT/ALAT)', 'price' => 4000],
                    ['name' => 'Hémoglobine Glyquée (HbA1c)', 'price' => 12000],
                    ['name' => 'Ionogramme Complet', 'price' => 15000],
                ]
            ],
            [
                'name' => 'Hématologie',
                'code' => 'LAB-HEM',
                'description' => 'Étude des cellules sanguines et de la coagulation.',
                'color' => '#ef4444', // Red
                'icon' => 'droplets',
                'prestations' => [
                    ['name' => 'NFS (Hémogramme Complet)', 'price' => 5000],
                    ['name' => 'Vitesse de Sédimentation (VS)', 'price' => 2000],
                    ['name' => 'Groupe Sanguin & Rhésus', 'price' => 3500],
                    ['name' => 'Taux de Prothrombine (TP)', 'price' => 4500],
                    ['name' => 'TCA', 'price' => 5000],
                    ['name' => 'Électrophorèse de l\'Hémoglobine', 'price' => 15000],
                    ['name' => 'Test de Coombs', 'price' => 8000],
                ]
            ],
            [
                'name' => 'Microbiologie & Bactériologie',
                'code' => 'LAB-MIC',
                'description' => 'Recherche d\'agents infectieux (bactéries, champignons).',
                'color' => '#10b981', // Emerald
                'icon' => 'microscope',
                'prestations' => [
                    ['name' => 'ECBU (Examen Cytobactériologique des Urines)', 'price' => 6000],
                    ['name' => 'Hémoculture', 'price' => 12000],
                    ['name' => 'Coproculture', 'price' => 8000],
                    ['name' => 'Prélèvement Vaginal / Urétral', 'price' => 7000],
                    ['name' => 'Antibiogramme', 'price' => 10000],
                    ['name' => 'Examen Direct au KOH', 'price' => 4000],
                ]
            ],
            [
                'name' => 'Immunologie & Sérologie',
                'code' => 'LAB-IMM',
                'description' => 'Tests immunologiques et recherche d\'anticorps.',
                'color' => '#8b5cf6', // Violet
                'icon' => 'shield-check',
                'prestations' => [
                    ['name' => 'TDR Paludisme', 'price' => 2000],
                    ['name' => 'Sérologie VIH 1&2', 'price' => 5000],
                    ['name' => 'Sérologie Hépatite B (AgHBs)', 'price' => 5000],
                    ['name' => 'Sérologie Hépatite C (VHC)', 'price' => 8000],
                    ['name' => 'Sérologie Syphilis (VDRL/TPHA)', 'price' => 4000],
                    ['name' => 'CRP (Protéine C Réactive)', 'price' => 5000],
                    ['name' => 'Facteur Rhumatoïde', 'price' => 6000],
                    ['name' => 'Bilan de fertilité (Hormones)', 'price' => 45000],
                ]
            ],
            [
                'name' => 'Parasitologie',
                'code' => 'LAB-PAR',
                'description' => 'Recherche de parasites dans les selles et le sang.',
                'color' => '#f59e0b', // Amber
                'icon' => 'bug',
                'prestations' => [
                    ['name' => 'Goutte Épaisse', 'price' => 2000],
                    ['name' => 'Examen Parasitologique des Selles', 'price' => 4000],
                    ['name' => 'Scotch Test', 'price' => 3000],
                ]
            ]
        ];

        foreach ($labServices as $sData) {
            $service = Service::updateOrCreate(
                [
                    'hospital_id' => $hospital->id,
                    'code' => $sData['code'] . '-' . $hospital->id
                ],
                [
                    'name' => $sData['name'],
                    'description' => $sData['description'],
                    'type' => 'technical',
                    'icon' => $sData['icon'],
                    'color' => $sData['color'],
                    'is_active' => true,
                    'consultation_price' => 0 // Pas de consultation directe au labo en général
                ]
            );

            foreach ($sData['prestations'] as $pData) {
                Prestation::updateOrCreate(
                    [
                        'hospital_id' => $hospital->id,
                        'service_id' => $service->id,
                        'name' => $pData['name']
                    ],
                    [
                        'code' => $sData['code'] . '-' . strtoupper(Str::slug($pData['name'])) . '-' . $hospital->id,
                        'price' => $pData['price'],
                        'category' => 'examen',
                        'is_active' => true,
                        'description' => $pData['name']
                    ]
                );
            }
        }

        $this->command->info("✓ Services de Laboratoire pro créés pour l'hôpital: " . $hospital->name);
    }
}
