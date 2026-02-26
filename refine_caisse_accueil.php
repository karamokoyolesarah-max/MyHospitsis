<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Service;

$hospitalId = 2;

echo "--- REFINING CAISSE ACCUEIL (Hospital $hospitalId) ---\n";

// Update Caisse Accueil to be the global one
$accueilCaisse = Service::where('hospital_id', $hospitalId)->where('name', 'like', 'Caisse Accueil%')->first();

if ($accueilCaisse) {
    $accueilCaisse->update([
        'name' => 'Caisse Accueil (Services Généraux)',
        'description' => 'Caisse centrale gérant tous les services sauf le Laboratoire et les Urgences',
        'parent_id' => null // Set to null as it's global, or we could link it to a 'Administration' service if it exists
    ]);
    echo "Updated '{$accueilCaisse->name}' to be global (excluding Lab/Urg).\n";
} else {
    echo "Caisse Accueil not found!\n";
}
