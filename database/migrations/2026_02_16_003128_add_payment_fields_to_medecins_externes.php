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
            $table->string('payment_orange_number')->nullable();
            $table->string('payment_mtn_number')->nullable();
            $table->string('payment_moov_number')->nullable();
            $table->string('payment_wave_number')->nullable();
            
            $table->string('payment_qr_orange')->nullable();
            $table->string('payment_qr_mtn')->nullable();
            $table->string('payment_qr_moov')->nullable();
            $table->string('payment_qr_wave')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medecins_externes', function (Blueprint $table) {
            $table->dropColumn([
                'payment_orange_number',
                'payment_mtn_number',
                'payment_moov_number',
                'payment_wave_number',
                'payment_qr_orange',
                'payment_qr_mtn',
                'payment_qr_moov',
                'payment_qr_wave'
            ]);
        });
    }
};
