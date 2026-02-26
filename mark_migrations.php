<?php

use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

DB::table('migrations')->insert([
    ['migration' => '2026_02_02_120000_update_appointments_status_enum_v2', 'batch' => 999],
    ['migration' => '2026_02_02_123052_add_patient_validation_to_appointments', 'batch' => 999]
]);

echo "Migrations marked as done.\n";
