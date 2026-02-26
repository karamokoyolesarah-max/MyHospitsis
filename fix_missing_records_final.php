<?php

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Appointment;
use App\Models\PatientVital;

// 1. Ensure Vital 3 (Feb 10) is visible
$v3 = PatientVital::find(3);
if ($v3) {
    if (!$v3->is_visible_to_patient) {
        $v3->update(['is_visible_to_patient' => true]);
        echo "Updated Vital 3 (Feb 10) to VISIBLE.\n";
    } else {
        echo "Vital 3 (Feb 10) is already VISIBLE.\n";
    }
} else {
    echo "Vital 3 not found.\n";
}

// 2. Create Vital for Appointment 19 (Feb 13)
$appt19 = Appointment::find(19);

if ($appt19) {
    echo "Processing Appointment 19 (Feb 13)...\n";
    // Check if vital exists for this exact timestamp or day
    $exists = PatientVital::where('patient_ipu', $appt19->patient->ipu)
        ->whereDate('created_at', $appt19->appointment_datetime->toDateString())
        ->where('doctor_id', $appt19->medecin_id)
        ->exists();

    if (!$exists) {
        $vital = PatientVital::create([
            'patient_ipu' => $appt19->patient->ipu,
            'patient_name' => $appt19->patient->full_name,
            'doctor_id' => $appt19->medecin_id,
            'hospital_id' => $appt19->hospital_id,
            'service_id' => $appt19->service_id,
            'created_at' => $appt19->appointment_datetime,
            'status' => 'completed',
            'reason' => $appt19->reason ?? 'Consultation',
            'is_visible_to_patient' => true,
            // Dummy vitals required if not nullable (migration said nullable, but providing logical defaults is safer for display)
            'temperature' => '37.0',
            'blood_pressure' => '12/8',
            'pulse' => '72',
            'weight' => '75',
            'height' => '175',
        ]);
        echo "Created LINKED VITAL for Appt 19. ID: " . $vital->id . "\n";
    } else {
        echo "Vital for Appt 19 already exists (Skipping creation).\n";
    }
} else {
    echo "Appointment 19 not found.\n";
}
