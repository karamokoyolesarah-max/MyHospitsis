<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration

{
    public function up(): void
    {
        Schema::table('medications', function (Blueprint $table) {
            $table->string('brand_name')->nullable()->after('name');
            $table->string('active_ingredient')->nullable()->after('brand_name');
            $table->string('therapeutic_class')->nullable()->after('active_ingredient');
            $table->decimal('unit_price', 12, 2)->default(0)->after('category');
        });
    }

    public function down(): void

    {
        Schema::table('medications', function (Blueprint $table) {
            $table->dropColumn(['brand_name', 'active_ingredient', 'therapeutic_class', 'unit_price']);
        });
    }
};
