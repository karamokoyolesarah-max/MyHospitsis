<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('external_doctor_prestations', function (Blueprint $table) {
            $table->dropColumn('duration_minutes');
            $table->decimal('commission_percentage', 5, 2)->default(0)->after('price');
        });
    }

    public function down(): void
    {
        Schema::table('external_doctor_prestations', function (Blueprint $table) {
            $table->dropColumn('commission_percentage');
            $table->integer('duration_minutes')->default(30)->after('price');
        });
    }
};
