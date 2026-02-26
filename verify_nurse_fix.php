<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

$hospitalId = 2;

echo "--- VERIFYING NURSE EXCLUSION (Hospital $hospitalId) ---\n";

// Case 1: Technical Pole
echo "\nChecking: Pôle=Technique (Labo)\n";
$techUsers = User::where('hospital_id', $hospitalId)->technical()->get();
foreach ($techUsers as $u) {
    echo " - {$u->name} (Role: {$u->role}, Service: ".($u->service ? $u->service->name : 'NONE').")\n";
}
echo "Total Technical: " . $techUsers->count() . "\n";

// Case 2: Medical Pole
echo "\nChecking: Pôle=Médical (Soins)\n";
$medUsers = User::where('hospital_id', $hospitalId)->medical()->get();
foreach ($medUsers as $u) {
    if (str_contains($u->role, 'nurse') || str_contains($u->name, 'Infirmier')) {
        echo " - {$u->name} (Role: {$u->role}, Service: ".($u->service ? $u->service->name : 'NONE').")\n";
    }
}
echo "Total Medical: " . $medUsers->count() . "\n";

// Case 3: Support Pole
echo "\nChecking: Pôle=Support (Caisse)\n";
$supportUsers = User::where('hospital_id', $hospitalId)->support()->get();
foreach ($supportUsers as $u) {
    echo " - {$u->name} (Role: {$u->role})\n";
}
echo "Total Support: " . $supportUsers->count() . "\n";
