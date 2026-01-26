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
            $table->dropColumn('plan_expires_at');
            $table->datetime('plan_expires_at')->nullable()->after('current_plan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medecins_externes', function (Blueprint $table) {
            $table->dropColumn('plan_expires_at');
            $table->timestamp('plan_expires_at')->nullable()->after('current_plan');
        });
    }
};
