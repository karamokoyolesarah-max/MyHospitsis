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
        Schema::table('appointments', function (Blueprint $table) {
            $table->foreignId('doctor_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            // Note: We cannot easily revert to non-nullable without ensuring all records have a doctor_id.
            // But for rollback purposes, we'll attempt it.
            // In a real production scenario, you might want to fill nulls with a default or leave it nullable.
            // $table->foreignId('doctor_id')->nullable(false)->change(); 
        });
    }
};
