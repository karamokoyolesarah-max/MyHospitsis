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
        echo "Running migration changes...\n";

        // 1. FIRST: Convert ENUM to VARCHAR to avoid truncation errors
        DB::statement("ALTER TABLE users MODIFY COLUMN role VARCHAR(50) NOT NULL");

        // 2. THEN: Update legacy roles safely (column is now VARCHAR, no truncation)
        DB::table('users')->where('role', 'external_doctor')->update(['role' => 'medecin_externe']);
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
