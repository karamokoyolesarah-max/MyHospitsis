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
        Schema::table('external_doctor_recharges', function (Blueprint $table) {
            $table->text('failure_reason')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('external_doctor_recharges', function (Blueprint $table) {
            $table->string('failure_reason')->nullable()->change();
        });
    }
};
