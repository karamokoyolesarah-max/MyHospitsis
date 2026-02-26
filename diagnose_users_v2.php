<?php

use App\Models\Service;
use App\Models\User;
use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$hospitalId = 2; // Assuming this is the relevant hospital

echo "--- STATS FOR HOSPITAL $hospitalId ---\n";
$users = User::where('hospital_id', $hospitalId)->get();
echo "Total users: " . $users->count() . "\n";

$roles = ['admin', 'doctor', 'nurse', 'cashier', 'lab_technician', 'administrative', 'internal_doctor', 'doctor_lab', 'pharmacist', 'secretary'];
foreach ($roles as $role) {
    $count = $users->where('role', $role)->count();
    $withService = $users->where('role', $role)->whereNotNull('service_id')->count();
    $withoutService = $users->where('role', $role)->whereNull('service_id')->count();
    echo "Role: $role | Total: $count | With Service: $withService | Without Service: $withoutService\n";
    
    if ($count > 0) {
        foreach ($users->where('role', $role) as $u) {
            $serviceType = $u->service ? $u->service->type : 'N/A';
            echo "  - User: {$u->name} | Service Type: $serviceType\n";
        }
    }
}

echo "\n--- SERVICE TYPES FOR HOSPITAL $hospitalId ---\n";
$services = Service::where('hospital_id', $hospitalId)->get();
foreach ($services as $s) {
    echo "Service: {$s->name} | Type: {$s->type}\n";
}
