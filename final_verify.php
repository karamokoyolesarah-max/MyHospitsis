<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

$hospitalId = 2;

echo "--- VERIFYING FIX (Hospital $hospitalId) ---\n";

// Case 1: Technical + Médecin
echo "\nChecking: Pôle=Technique, Rôle=Médecin\n";
$users = User::where('hospital_id', $hospitalId)
    ->technical()
    ->whereIn('role', ['doctor', 'medecin', 'internal_doctor', 'doctor_lab', 'doctor_radio'])
    ->get();

foreach ($users as $u) {
    echo " - {$u->name} (Role: {$u->role}, Service: ".($u->service ? $u->service->name : 'NONE').")\n";
}
echo "Total: " . $users->count() . "\n";

// Case 2: Support + Caissier
echo "\nChecking: Pôle=Support, Rôle=Caissier\n";
$users = User::where('hospital_id', $hospitalId)
    ->support()
    ->where('role', 'cashier')
    ->get();

foreach ($users as $u) {
    echo " - {$u->name} (Role: {$u->role}, Service: ".($u->service ? $u->service->name : 'NONE').")\n";
}
echo "Total: " . $users->count() . "\n";
