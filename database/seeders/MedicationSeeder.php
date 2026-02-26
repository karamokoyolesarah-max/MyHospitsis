<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\Medication;
class MedicationSeeder extends Seeder

{
    public function run(): void
    {
        $medications = [
            [
                'name' => 'PARACETAMOL 500MG',
                'brand_name' => 'DOLIPRANE',
                'active_ingredient' => 'Paracétamol',
                'therapeutic_class' => 'Analgésique / Antipyrétique',
                'form' => 'Tablet',
                'dosage' => '500mg',
                'manufacturer' => 'Sanofi',
                'category' => 'Douleurs et Fièvre',
                'unit_price' => 1500,
                'description' => 'Traitement symptomatique des douleurs d\'intensité légère à modérée et/ou des états fébriles.',
            ],
            [
                'name' => 'AMOXICILLINE 500MG',
                'brand_name' => 'CLAMOXYL',
                'active_ingredient' => 'Amoxicilline',
                'therapeutic_class' => 'Antibiotique / Pénicilline',
                'form' => 'Tablet',
                'dosage' => '500mg',
                'manufacturer' => 'GSK',
                'category' => 'Infections',
                'unit_price' => 4500,
                'description' => 'Antibiotique de la famille des bétalactamines, du groupe des aminopénicillines.',
            ],
            [
                'name' => 'METFORMINE 850MG',
                'brand_name' => 'GLUCOPHAGE',
                'active_ingredient' => 'Metformine',
                'therapeutic_class' => 'Antidiabétique Oral',
                'form' => 'Tablet',
                'dosage' => '850mg',
                'manufacturer' => 'Merck',
                'category' => 'Diabète',
                'unit_price' => 3200,
                'description' => 'Traitement du diabète de type 2, en particulier en cas de surcharge pondérale.',
            ],
            [
                'name' => 'IBUPROFENE 400MG',
                'brand_name' => 'ADVIL',
                'active_ingredient' => 'Ibuprofène',
                'therapeutic_class' => 'Anti-inflammatoire non stéroïdien (AINS)',
                'form' => 'Tablet',
                'dosage' => '400mg',
                'manufacturer' => 'Pfizer',
                'category' => 'Douleurs et Inflammation',
                'unit_price' => 2800,
                'description' => 'Traitement de courte durée de la fièvre et/ou des douleurs.',
            ],
            [
                'name' => 'AMELIP 10MG',
                'brand_name' => 'TAHOR',
                'active_ingredient' => 'Atorvastatine',
                'therapeutic_class' => 'Hypolipidémiant / Statine',
                'form' => 'Tablet',
                'dosage' => '10mg',
                'manufacturer' => 'Viatris',
                'category' => 'Cardiologie',
                'unit_price' => 8500,
                'description' => 'Réduit les taux de cholestérol et de triglycérides dans le sang.',
            ],
            [
                'name' => 'VENTOLINE 100MCG',
                'brand_name' => 'VENTOLINE',
                'active_ingredient' => 'Salbutamol',
                'therapeutic_class' => 'Bronchodilatateur / Bêta-2 mimétique',
                'form' => 'Inhaler',
                'dosage' => '100mcg/dose',
                'manufacturer' => 'GSK',
                'category' => 'Pneumologie',
                'unit_price' => 3800,
                'description' => 'Traitement symptomatique de la crise d\'asthme et des bronchospasmes.',
            ],
            [
                'name' => 'SPASFON',
                'brand_name' => 'SPASFON',
                'active_ingredient' => 'Phloroglucinol',
                'therapeutic_class' => 'Antispasmodique musculotrope',
                'form' => 'Tablet',
                'dosage' => '80mg',
                'manufacturer' => 'Teva',
                'category' => 'Gastro-entérologie',
                'unit_price' => 2200,
                'description' => 'Traitement des douleurs spasmodiques de l\'intestin, des voies biliaires, de la vessie et de l\'utérus.',
            ],
            [
                'name' => 'MOPRAL 20MG',
                'brand_name' => 'MOPRAL',
                'active_ingredient' => 'Oméprazole',
                'therapeutic_class' => 'Inhibiteur de la pompe à protons (IPP)',
                'form' => 'Capsule',
                'dosage' => '20mg',
                'manufacturer' => 'AstraZeneca',
                'category' => 'Gastro-entérologie',
                'unit_price' => 5400,
                'description' => 'Traitement du reflux gastro-oesophagien et des ulcères gastriques.',
            ],
        ];

        foreach ($medications as $med) {

            Medication::updateOrCreate(['name' => $med['name']], $med);
        }
    }
}
