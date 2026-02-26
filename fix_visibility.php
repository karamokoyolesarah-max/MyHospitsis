<?php

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\PatientVital;

// Fix the specific record ID 4 found in inspection
$id = 4;
$vital = PatientVital::find($id);

if ($vital) {
    echo "Updating Vital ID {$id} visibility...\n";
    $vital->update(['is_visible_to_patient' => true]);
    echo "Done. New Visibility: " . ($vital->is_visible_to_patient ? 'YES' : 'NO') . "\n";
} else {
    echo "Vital ID {$id} not found.\n";
}
