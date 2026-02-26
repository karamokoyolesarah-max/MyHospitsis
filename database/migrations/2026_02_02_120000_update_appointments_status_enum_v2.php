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
        // Add missing statuses for external doctor tracking
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE appointments MODIFY COLUMN status ENUM('scheduled', 'confirmed', 'cancelled', 'completed', 'prepared', 'pending_payment', 'paid', 'released', 'pending', 'accepted', 'on_the_way', 'arrived') DEFAULT 'scheduled'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to the state before these tracking statuses were added
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE appointments MODIFY COLUMN status ENUM('scheduled', 'confirmed', 'cancelled', 'completed', 'prepared', 'pending_payment', 'paid', 'released', 'pending') DEFAULT 'scheduled'");
        }
    }
};
