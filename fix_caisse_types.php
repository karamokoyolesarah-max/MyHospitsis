<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Service;

$hospitalId = 2;

echo "--- UPDATING CAISSE TYPES FOR H2 ---\n";

$labCaisse = Service::where('hospital_id', $hospitalId)->where('name', 'Caisse Laboratoire')->first();
if ($labCaisse) {
    $labCaisse->update(['caisse_type' => 'labo']);
    echo "ID: {$labCaisse->id} | Name: {$labCaisse->name} -> caisse_type: labo\n";
}

$urgencesCaisse = Service::where('hospital_id', $hospitalId)->where('name', 'Caisse Urgences')->first();
if ($urgencesCaisse) {
    $urgencesCaisse->update(['caisse_type' => 'urgence']);
    echo "ID: {$urgencesCaisse->id} | Name: {$urgencesCaisse->name} -> caisse_type: urgence\n";
}

$accueilCaisse = Service::where('hospital_id', $hospitalId)->where('name', 'like', 'Caisse Accueil%')->first();
if ($accueilCaisse) {
    $accueilCaisse->update(['caisse_type' => 'standard']);
    echo "ID: {$accueilCaisse->id} | Name: {$accueilCaisse->name} -> caisse_type: standard\n";
}

echo "--- VERIFYING ALL SERVICES FOR H2 ---\n";
foreach(Service::where('hospital_id', $hospitalId)->get() as $s) {
    echo "ID: {$s->id} | Name: {$s->name} | Type: {$s->type} | Caisse: " . ($s->is_caisse ? 'Y' : 'N') . " | CType: {$s->caisse_type} | Parent: " . ($s->parent_id ?? 'None') . "\n";
}
