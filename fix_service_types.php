<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Service;
use Illuminate\Support\Facades\DB;

$hospitalId = 2;

$mappings = [
    'Laboratoire d\'Analyses' => 'technical',
    'Radiologie & Imagerie' => 'technical',
    'Caisse' => 'support',
    'Consultations Générales' => 'medical',
];

echo "--- UPDATING SERVICES FOR HOSPITAL $hospitalId ---\n";

foreach ($mappings as $name => $type) {
    $service = Service::where('hospital_id', $hospitalId)
        ->where('name', 'like', "%$name%")
        ->first();
    
    if ($service) {
        $oldType = $service->type;
        $service->update(['type' => $type]);
        echo "Updated Service: {$service->name} | Old Type: $oldType | New Type: $type\n";
    } else {
        echo "Service NOT FOUND: $name\n";
    }
}
