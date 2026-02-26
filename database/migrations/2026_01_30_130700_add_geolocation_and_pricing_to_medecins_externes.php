<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('medecins_externes', function (Blueprint $table) {
            if (!Schema::hasColumn('medecins_externes', 'latitude')) {
                $table->decimal('latitude', 10, 8)->nullable();
            }
            if (!Schema::hasColumn('medecins_externes', 'longitude')) {
                $table->decimal('longitude', 11, 8)->nullable();
            }
            if (!Schema::hasColumn('medecins_externes', 'travel_fee_type')) {
                $table->enum('travel_fee_type', ['fixed', 'per_km', 'combined'])->default('combined');
            }
            if (!Schema::hasColumn('medecins_externes', 'base_travel_fee')) {
                $table->decimal('base_travel_fee', 10, 2)->default(5000);
            }
            if (!Schema::hasColumn('medecins_externes', 'travel_fee_per_km')) {
                $table->decimal('travel_fee_per_km', 10, 2)->default(500);
            }
            if (!Schema::hasColumn('medecins_externes', 'max_travel_distance')) {
                $table->integer('max_travel_distance')->default(30);
            }
            if (!Schema::hasColumn('medecins_externes', 'consultation_price')) {
                $table->decimal('consultation_price', 10, 2)->default(15000);
            }
        });
    }

    public function down(): void
    {
        Schema::table('medecins_externes', function (Blueprint $table) {
            $table->dropColumn([
                'latitude', 'longitude', 'travel_fee_type', 'base_travel_fee', 
                'travel_fee_per_km', 'max_travel_distance', 'consultation_price'
            ]);
        });
    }
};
