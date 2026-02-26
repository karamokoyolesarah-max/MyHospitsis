<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Service;

$hospitalId = 2;

// Generate a code based on name if possible
$name = 'Caisse Laboratoire';
$code = 'CAISSE-LABO';

$service = Service::create([
    'name' => $name,
    'code' => $code,
    'type' => 'support',
    'hospital_id' => $hospitalId,
    'is_active' => true,
    'is_caisse' => true,
    'caisse_type' => 'standard',
    'description' => 'Service de facturation et encaissement pour le laboratoire'
]);

echo "Created Service: {$service->name} (ID: {$service->id}) with Code: {$service->code}\n";
