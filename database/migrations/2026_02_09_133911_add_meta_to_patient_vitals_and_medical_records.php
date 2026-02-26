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
        Schema::table('patient_vitals', function (Blueprint $table) {
            if (!Schema::hasColumn('patient_vitals', 'meta')) {
                $table->json('meta')->nullable()->after('ordonnance');
            }
        });

        Schema::table('medical_records', function (Blueprint $table) {
            if (!Schema::hasColumn('medical_records', 'meta')) {
                $table->json('meta')->nullable()->after('content');
            }
        });
    }

    public function down(): void
    {
        Schema::table('patient_vitals', function (Blueprint $table) {
            $table->dropColumn('meta');
        });

        Schema::table('medical_records', function (Blueprint $table) {
            $table->dropColumn('meta');
        });
    }
};
