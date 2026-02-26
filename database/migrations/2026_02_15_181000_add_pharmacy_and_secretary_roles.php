<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add 'pharmacist' and 'secretary' to the role ENUM
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
            'doctor_radio',
            'radio_technician',
            'receptionist',
            'external_doctor',
            'pharmacist',
            'secretary'
        ) NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to VARCHAR for safety or remove the roles if necessary
        DB::statement("ALTER TABLE users MODIFY COLUMN role VARCHAR(50) NOT NULL");
    }
};
