<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// 1. Log in
$doctor = \App\Models\User::where('role', 'doctor')->first();
auth()->guard('web')->login($doctor);

// 2. Fetch latest appointment
$appointment = \App\Models\Appointment::latest()->first();

echo "Appointment ID: " . $appointment->id . "\n";
echo "Patient ID: " . ($appointment->patient_id ?? 'NULL') . "\n";

// 3. Diagnose Patient
if ($appointment->patient_id) {
    // Check purely raw
    $raw = \Illuminate\Support\Facades\DB::table('patients')->where('id', $appointment->patient_id)->first();
    if ($raw) {
        echo "DB Row Exists.\n";
        echo "Deleted At: " . ($raw->deleted_at ?? 'NULL') . "\n";
        echo "Hospital ID: " . ($raw->hospital_id ?? 'NULL') . "\n";
    } else {
        echo "DB Row MISSING.\n";
    }
    
    // Check Relation with constraints
    $rel = $appointment->patient;
    echo "Relation Result: " . ($rel ? "FOUND" : "NULL") . "\n";
}
