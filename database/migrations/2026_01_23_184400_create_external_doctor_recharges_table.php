<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('external_doctor_recharges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medecin_externe_id')->constrained('medecins_externes')->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->string('payment_method'); // mtn, orange, wave
            $table->string('transaction_id')->nullable();
            $table->string('phone_number');
            $table->enum('status', ['pending', 'completed', 'failed'])->default('pending');
            $table->text('response_data')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('external_doctor_recharges');
    }
};
