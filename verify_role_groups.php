<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

$hospitalId = 2;

echo "--- Testing: Pôle=technical, Role=doctor ---\n";
// Simulating request params
$users = User::where('hospital_id', $hospitalId)
    ->technical()
    ->whereIn('role', ['doctor', 'medecin', 'internal_doctor', 'doctor_lab', 'doctor_radio']) // The logic in controller
    ->get();

foreach ($users as $u) {
    echo " - {$u->name} (Role: {$u->role})\n";
}
echo "Total: " . $users->count() . "\n";
