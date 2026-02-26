<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Service;

$hospitalId = 2;

$service = Service::create([
    'name' => 'Caisse Laboratoire',
    'type' => 'support',
    'hospital_id' => $hospitalId,
    'is_active' => true,
    'description' => 'Service de facturation et encaissement pour le laboratoire'
]);

echo "Created Service: {$service->name} (ID: {$service->id}) for Hospital $hospitalId\n";
