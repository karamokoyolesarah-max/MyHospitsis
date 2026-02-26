<?php

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Patient;
use App\Models\Appointment;
use App\Models\PatientVital;

$ipu = 'PAT202633151';
$patient = Patient::where('ipu', $ipu)->first();

if (!$patient) {
    echo "Patient not found.\n";
    exit;
}

echo "--- Appointments ---\n";
$appointments = Appointment::where('patient_id', $patient->id)->orderBy('appointment_datetime', 'desc')->get();
foreach ($appointments as $appt) {
    echo "ID: {$appt->id} | Date: {$appt->appointment_datetime} | Status: {$appt->status} | Doctor: " . ($appt->doctor->name ?? 'N/A') . "\n";
}

echo "\n--- Medical Records (PatientVital) ---\n";
$vitals = PatientVital::where('patient_ipu', $ipu)->orderBy('created_at', 'desc')->get();
foreach ($vitals as $vital) {
    echo "ID: {$vital->id} | Date: {$vital->created_at} | Status: {$vital->status} | Visible: " . ($vital->is_visible_to_patient ? 'YES' : 'NO') . " | Reason: {$vital->reason}\n";
}
