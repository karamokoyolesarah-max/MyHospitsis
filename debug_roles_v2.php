<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

$hospitalId = 2;

echo "--- LISTING ALL USERS FOR HOSPITAL $hospitalId ---\n";
$users = User::where('hospital_id', $hospitalId)->get();
foreach ($users as $u) {
    echo "ID: {$u->id} | Name: {$u->name} | Role: {$u->role} | S_ID: ".($u->service_id ?? 'NULL')."\n";
}

echo "\n--- TECHNICAL POLE USERS ---\n";
$techUsers = User::where('hospital_id', $hospitalId)->technical()->get();
foreach ($techUsers as $u) {
    echo "Name: {$u->name} | Role: {$u->role}\n";
}
