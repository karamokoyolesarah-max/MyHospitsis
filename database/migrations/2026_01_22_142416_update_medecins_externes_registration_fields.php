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
            $table->string('adresse_residence')->nullable()->after('adresse_cabinet');
            $table->string('diplome_path')->nullable()->after('adresse_residence');
            $table->string('id_card_recto_path')->nullable()->after('diplome_path');
            $table->string('id_card_verso_path')->nullable()->after('id_card_recto_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medecins_externes', function (Blueprint $table) {
            $table->dropColumn(['adresse_residence', 'diplome_path', 'id_card_recto_path', 'id_card_verso_path']);
        });
    }
};
