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
        // Add 'pending' to the allowed status values
        DB::statement("ALTER TABLE appointments MODIFY COLUMN status ENUM('scheduled', 'confirmed', 'cancelled', 'completed', 'prepared', 'pending_payment', 'paid', 'released', 'pending') DEFAULT 'scheduled'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to previous enum values (warning: 'pending' values might cause issues if not handled)
        // In a real scenario, we might want to change them to 'scheduled' before reverting.
        DB::statement("UPDATE appointments SET status = 'scheduled' WHERE status = 'pending'");
        DB::statement("ALTER TABLE appointments MODIFY COLUMN status ENUM('scheduled', 'confirmed', 'cancelled', 'completed', 'prepared', 'pending_payment', 'paid', 'released') DEFAULT 'scheduled'");
    }
};
