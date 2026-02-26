<?php

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Patient;
use App\Models\Appointment;
use App\Models\PatientVital;

$output = "";
$ipu = 'PAT202633151';
$patient = Patient::where('ipu', $ipu)->first();

if (!$patient) {
    file_put_contents('history_output.txt', "Patient not found.");
    exit;
}

$output .= "--- Appointments ---\n";
$appointments = Appointment::where('patient_id', $patient->id)->with('doctor')->orderBy('appointment_datetime', 'asc')->get();
foreach ($appointments as $appt) {
    if (!$appt) continue;
    $date = $appt->appointment_datetime->toDateString();
    $output .= "Appt ID: {$appt->id} | Date: {$date} | Status: {$appt->status} | Doctor ID: {$appt->medecin_id}\n";
    
    // Check if a vital exists for this date
    $vital = PatientVital::where('patient_ipu', $ipu)
        ->whereDate('created_at', $date)
        ->first();
    
    if ($vital) {
        $vis = $vital->is_visible_to_patient ? 'YES' : 'NO';
        $output .= "   -> LINKED VITAL FOUND: ID {$vital->id} | Visible: {$vis}\n";
    } else {
        $output .= "   -> NO VITAL FOUND for this date.\n";
    }
}

$output .= "\n--- All Vitals for Patient ---\n";
$vitals = PatientVital::where('patient_ipu', $ipu)->orderBy('created_at', 'desc')->get();
foreach ($vitals as $v) {
    $vis = $v->is_visible_to_patient ? 'YES' : 'NO';
    $output .= "Vital ID: {$v->id} | Date: {$v->created_at} | Visible: {$vis}\n";
}

file_put_contents('history_output.txt', $output);
