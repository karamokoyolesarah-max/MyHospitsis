<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            if (!Schema::hasColumn('appointments', 'medecin_externe_id')) {
                $table->foreignId('medecin_externe_id')->nullable()->constrained('medecins_externes')->nullOnDelete();
            }
            if (!Schema::hasColumn('appointments', 'doctor_current_latitude')) {
                $table->decimal('doctor_current_latitude', 10, 8)->nullable();
            }
            if (!Schema::hasColumn('appointments', 'doctor_current_longitude')) {
                $table->decimal('doctor_current_longitude', 11, 8)->nullable();
            }
            if (!Schema::hasColumn('appointments', 'estimated_arrival_time')) {
                $table->dateTime('estimated_arrival_time')->nullable();
            }
            if (!Schema::hasColumn('appointments', 'travel_started_at')) {
                $table->dateTime('travel_started_at')->nullable();
            }
            if (!Schema::hasColumn('appointments', 'travel_completed_at')) {
                $table->dateTime('travel_completed_at')->nullable();
            }
            if (!Schema::hasColumn('appointments', 'calculated_distance_km')) {
                $table->decimal('calculated_distance_km', 8, 2)->nullable();
            }
            if (!Schema::hasColumn('appointments', 'calculated_travel_fee')) {
                $table->decimal('calculated_travel_fee', 10, 2)->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropForeign(['medecin_externe_id']);
            $table->dropColumn([
                'medecin_externe_id', 'doctor_current_latitude', 'doctor_current_longitude',
                'estimated_arrival_time', 'travel_started_at', 'travel_completed_at',
                'calculated_distance_km', 'calculated_travel_fee'
            ]);
        });
    }
};
