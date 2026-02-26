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
        // Debug info
        echo "Running migration changes...\n";

        // 0. Update legacy roles if any
        DB::statement("UPDATE users SET role='medecin_externe' WHERE role='external_doctor'");
        DB::statement("UPDATE users SET role='doctor' WHERE role='doctor' AND role NOT IN ('admin', 'nurse', 'cashier', 'administrative', 'internal_doctor', 'medecin_externe', 'lab_technician', 'doctor_lab', 'doctor_radio', 'radio_technician', 'receptionist', 'external_doctor')"); // No-op but safe check?


        // 1. D'abord convertir en VARCHAR pour éviter les erreurs de truncation si l'ordre change ou autre
        DB::statement("ALTER TABLE users MODIFY COLUMN role VARCHAR(50) NOT NULL");
        
        // 2. Ensuite appliquer le nouvel ENUM avec TOUS les rôles possibles
        /*
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'doctor', 'nurse', 'cashier', 'administrative', 'internal_doctor', 'medecin_externe', 'lab_technician', 'doctor_lab', 'doctor_radio', 'radio_technician', 'receptionist', 'external_doctor') NOT NULL");
        */
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Retirer 'lab_technician' de l'ENUM
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'doctor', 'nurse', 'cashier', 'administrative', 'internal_doctor', 'medecin_externe') NOT NULL");
    }
};
