<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Service;

$hospitalId = 2;

echo "--- LINKING & CREATING CASHIER SERVICES (Hospital $hospitalId) ---\n";

// 1. Link Caisse Laboratoire
$labCaisse = Service::where('hospital_id', $hospitalId)->where('name', 'Caisse Laboratoire')->first();
$labMain = Service::where('hospital_id', $hospitalId)->where('name', 'like', '%Laboratoire%')->where('type', 'technical')->first();

if ($labCaisse && $labMain) {
    $labCaisse->update(['parent_id' => $labMain->id]);
    echo "Linked '{$labCaisse->name}' to '{$labMain->name}' (ID: {$labMain->id})\n";
} else {
    echo "Could not link Caisse Laboratoire. Caisse: ".($labCaisse?'Found':'Missing').", Main: ".($labMain?'Found':'Missing')."\n";
}

// 2. Create & Link Caisse Urgences
$emergenciesMain = Service::where('hospital_id', $hospitalId)->where('name', 'like', '%Urgences%')->first();
if ($emergenciesMain) {
    $emergenciesCaisse = Service::updateOrCreate(
        ['hospital_id' => $hospitalId, 'name' => 'Caisse Urgences'],
        [
            'code' => 'CAISSE-URG',
            'type' => 'support',
            'is_active' => true,
            'is_caisse' => true,
            'caisse_type' => 'standard',
            'parent_id' => $emergenciesMain->id,
            'description' => 'Caisse dédiée aux urgences'
        ]
    );
    echo "Created/Updated '{$emergenciesCaisse->name}' linked to '{$emergenciesMain->name}'\n";
}

// 3. Create & Link Caisse Accueil
$accueilMain = Service::where('hospital_id', $hospitalId)->where('name', 'like', '%Générale%')->first(); // Assuming general med is accueil
if ($accueilMain) {
    $accueilCaisse = Service::updateOrCreate(
        ['hospital_id' => $hospitalId, 'name' => 'Caisse Accueil'],
        [
            'code' => 'CAISSE-ACC',
            'type' => 'support',
            'is_active' => true,
            'is_caisse' => true,
            'caisse_type' => 'standard',
            'parent_id' => $accueilMain->id,
            'description' => 'Caisse de l\'accueil principal'
        ]
    );
    echo "Created/Updated '{$accueilCaisse->name}' linked to '{$accueilMain->name}'\n";
}
