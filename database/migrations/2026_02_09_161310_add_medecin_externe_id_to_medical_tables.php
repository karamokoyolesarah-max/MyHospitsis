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
        Schema::table('prescriptions', function (Blueprint $table) {
            $table->foreignId('medecin_externe_id')->nullable()->constrained('medecins_externes')->nullOnDelete();
        });

        Schema::table('patient_vitals', function (Blueprint $table) {
            $table->foreignId('medecin_externe_id')->nullable()->constrained('medecins_externes')->nullOnDelete();
        });

        Schema::table('lab_requests', function (Blueprint $table) {
            $table->foreignId('medecin_externe_id')->nullable()->constrained('medecins_externes')->nullOnDelete();
        });

        Schema::table('medical_documents', function (Blueprint $table) {
            $table->foreignId('medecin_externe_id')->nullable()->constrained('medecins_externes')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prescriptions', function (Blueprint $table) { $table->dropForeign(['medecin_externe_id']); $table->dropColumn('medecin_externe_id'); });
        Schema::table('patient_vitals', function (Blueprint $table) { $table->dropForeign(['medecin_externe_id']); $table->dropColumn('medecin_externe_id'); });
        Schema::table('lab_requests', function (Blueprint $table) { $table->dropForeign(['medecin_externe_id']); $table->dropColumn('medecin_externe_id'); });
        Schema::table('medical_documents', function (Blueprint $table) { $table->dropForeign(['medecin_externe_id']); $table->dropColumn('medecin_externe_id'); });
    }
};
