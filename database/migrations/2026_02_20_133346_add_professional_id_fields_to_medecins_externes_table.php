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
        Schema::table('medecins_externes', function (Blueprint $table) {
            $table->string('numero_ordre')->nullable()->change();
            $table->string('numero_matricule')->nullable()->after('numero_ordre');
            $table->string('numero_diplome')->nullable()->after('numero_matricule');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medecins_externes', function (Blueprint $table) {
            $table->string('numero_ordre')->nullable(false)->change();
            $table->dropColumn(['numero_matricule', 'numero_diplome']);
        });
    }
};
