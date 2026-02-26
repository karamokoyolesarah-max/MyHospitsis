<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        echo "Running: " . __FILE__ . "\n";
        // Add radiology roles to the existing ENUM
        /*
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM(
            'admin',
            'doctor',
            'nurse',
            'cashier',
            'administrative',
            'internal_doctor',
            'medecin_externe',
            'lab_technician',
            'doctor_lab',
            'external_doctor',
            'radio_technician',
            'doctor_radio'
        ) NOT NULL");
        */
        // Fallback to VARCHAR to allow migration to pass if ENUM fails
        DB::statement("ALTER TABLE users MODIFY COLUMN role VARCHAR(50) NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove radiology roles from ENUM (revert to original)
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM(
            'admin',
            'doctor',
            'nurse',
            'cashier',
            'administrative',
            'internal_doctor',
            'medecin_externe',
            'lab_technician',
            'doctor_lab',
            'external_doctor'
        ) NOT NULL");
    }
};
