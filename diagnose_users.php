<?php

use App\Models\Service;
use App\Models\User;
use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- HOSPITAL IDS ---\n";
$hospitals = DB::table('hospitals')->pluck('name', 'id');
foreach ($hospitals as $id => $name) {
    echo "ID: $id | Name: $name\n";
}

echo "\n--- SERVICES ---\n";
$services = Service::all();
foreach ($services as $s) {
    echo "ID: {$s->id} | Name: {$s->name} | Type: {$s->type} | Hospital ID: {$s->hospital_id}\n";
}

echo "\n--- USERS (Filtered roles) ---\n";
$users = User::whereIn('role', ['cashier', 'lab_technician', 'lab_doctor', 'doctor_lab', 'pharmacist', 'secretary'])
    ->get();

foreach ($users as $u) {
    echo "ID: {$u->id} | Name: {$u->name} | Role: {$u->role} | Service ID: " . ($u->service_id ?? 'NULL') . " | Hospital ID: {$u->hospital_id}\n";
    if ($u->service_id) {
        $service = Service::find($u->service_id);
        if ($service) {
            echo "  └─ Service Name: {$service->name} | Service Type: {$service->type}\n";
        } else {
            echo "  └─ Service not found!\n";
        }
    }
}
