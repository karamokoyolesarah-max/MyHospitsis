<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Service;

$hospitalId = 2;
$service = Service::where('hospital_id', $hospitalId)->where('name', 'Caisse Laboratoire')->first();

if ($service) {
    echo "Service Found: {$service->name}\n";
    echo "Type (Pole): {$service->type}\n";
    echo "Status: " . ($service->is_active ? 'Active' : 'Inactive') . "\n";
    
    if ($service->type === 'support') {
        echo "SUCCESS: This service will appear under the Support pole.\n";
    } else {
        echo "FAILURE: This service will NOT appear under the Support pole.\n";
    }
} else {
    echo "FAILURE: Service not found.\n";
}
