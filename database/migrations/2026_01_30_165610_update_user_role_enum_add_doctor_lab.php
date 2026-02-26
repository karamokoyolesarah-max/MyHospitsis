<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        echo "Running: " . __FILE__ . "\n";
        // First convert to string to avoid truncation issues
        DB::statement("ALTER TABLE users MODIFY COLUMN role VARCHAR(100) NOT NULL");
        
        // Then convert to enum with all required roles
        /*
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'doctor', 'nurse', 'cashier', 'administrative', 'internal_doctor', 'medecin_externe', 'lab_technician', 'doctor_lab', 'external_doctor') NOT NULL");
        */
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
