<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('medecins_externes', function (Blueprint $table) {
            $table->boolean('is_available')->default(false)->after('statut');
            $table->decimal('balance', 10, 2)->default(0)->after('is_available');
            $table->string('current_plan')->nullable()->after('balance');
            $table->timestamp('plan_expires_at')->nullable()->after('current_plan');
        });
    }

    public function down(): void
    {
        Schema::table('medecins_externes', function (Blueprint $table) {
            $table->dropColumn(['is_available', 'balance', 'current_plan', 'plan_expires_at']);
        });
    }
};
