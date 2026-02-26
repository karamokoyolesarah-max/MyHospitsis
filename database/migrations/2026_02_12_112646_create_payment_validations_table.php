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
        Schema::create('payment_validations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hospital_id')->constrained()->onDelete('cascade');
            $table->foreignId('invoice_id')->constrained()->onDelete('cascade');
            $table->string('payment_reference'); // Référence donnée par le patient
            $table->string('mobile_operator'); // wave, mtn, orange, moov
            $table->string('mobile_number'); // Numéro du patient
            $table->decimal('amount', 10, 2); // Montant payé
            $table->foreignId('validated_by')->constrained('users')->onDelete('cascade'); // Caissière
            $table->timestamp('validated_at');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Index pour recherche rapide
            $table->index('payment_reference');
            $table->index('validated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_validations');
    }
};
