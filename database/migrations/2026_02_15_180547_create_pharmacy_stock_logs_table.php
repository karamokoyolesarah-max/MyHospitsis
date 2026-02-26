<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pharmacy_stock_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hospital_id')->constrained()->onDelete('cascade');
            $table->foreignId('pharmacy_stock_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained(); // Who made the movement
            $table->integer('quantity'); // Positive for entry, negative for exit
            $table->enum('type', ['entry', 'exit', 'adjustment', 'transfer', 'return', 'expired']);
            $table->string('reason')->nullable();
            $table->string('reference_id')->nullable(); // Relation to an order or prescription
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pharmacy_stock_logs');
    }
};
