<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

$techs = User::whereIn('role', ['lab_technician', 'radio_technician', 'doctor_lab', 'doctor_radio'])->with('service')->get();
foreach ($techs as $u) {
    echo "ID: {$u->id} | Name: {$u->name} | Role: {$u->role} | Service: " . ($u->service ? $u->service->name : 'None') . " (Type: " . ($u->service ? $u->service->type : 'N/A') . ")\n";
}
