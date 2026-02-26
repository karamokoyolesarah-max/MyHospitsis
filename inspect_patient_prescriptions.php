<?php

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Patient;
use App\Models\Prescription;
use App\Models\PatientVital;

// ID from screenshot
$ipu = 'PAT202633151';

$output = "";

$patient = Patient::where('ipu', $ipu)->first();

if (!$patient) {
    $output .= "Patient not found with IPU: $ipu\n";
    file_put_contents('inspection_output.txt', $output);
    exit;
}

$output .= "Patient Found: {$patient->first_name} {$patient->last_name} (ID: {$patient->id})\n";

$output .= "\n--- Checking 'prescriptions' table ---\n";
$prescriptions = Prescription::where('patient_id', $patient->id)->get();
$output .= "Count: " . $prescriptions->count() . "\n";
foreach ($prescriptions as $p) {
    if (!$p instanceof Prescription) continue;
    $visible = $p->is_visible_to_patient ? 'YES' : 'NO';
    $output .= "ID: {$p->id} | Category: {$p->category} | Visible: {$visible} | Date: {$p->created_at}\n";
}

$output .= "\n--- Checking 'patient_vitals' table (ordonnance field) ---\n";
$vitals = PatientVital::where('patient_ipu', $ipu)->get();
$vitalsWithPrescription = $vitals->filter(function($v) {
    return !empty($v->ordonnance);
});

$output .= "Vitals with Ordonnance: " . $vitalsWithPrescription->count() . "\n";

foreach ($vitalsWithPrescription as $v) {
    $visible = $v->is_visible_to_patient ? 'YES' : 'NO';
    $len = strlen($v->ordonnance);
    $output .= "ID: {$v->id} | Visible: {$visible} | Date: {$v->created_at} | Length: {$len}\n";
}

file_put_contents('inspection_output.txt', $output);
