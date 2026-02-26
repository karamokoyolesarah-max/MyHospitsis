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
        if (!Schema::hasColumn('appointments', 'patient_confirmation_start_at')) {
            Schema::table('appointments', function (Blueprint $table) {
                $table->timestamp('patient_confirmation_start_at')->nullable()->after('status');
                $table->timestamp('patient_confirmation_end_at')->nullable()->after('patient_confirmation_start_at');
                $table->integer('rating_stars')->nullable()->after('patient_confirmation_end_at');
                $table->text('rating_comment')->nullable()->after('rating_stars');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn(['patient_confirmation_start_at', 'patient_confirmation_end_at', 'rating_stars', 'rating_comment']);
        });
    }
};
