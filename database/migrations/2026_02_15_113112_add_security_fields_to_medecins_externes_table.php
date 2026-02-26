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
            // OTP & Email Verification
            $table->boolean('is_email_verified')->default(false)->after('email');
            $table->string('otp_code')->nullable()->after('is_email_verified');
            $table->timestamp('otp_expires_at')->nullable()->after('otp_code');
            
            // Video Verification (KYC)
            $table->string('video_verification_path')->nullable()->after('id_card_verso_path');
            
            // Affiliation / Contact Référent
            $table->string('affiliation_type')->nullable()->comment('hospital, supervisor')->after('video_verification_path');
            $table->string('affiliation_name')->nullable()->after('affiliation_type');
            $table->string('affiliation_contact')->nullable()->after('affiliation_name'); // Phone or Email
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medecins_externes', function (Blueprint $table) {
            $table->dropColumn([
                'is_email_verified',
                'otp_code',
                'otp_expires_at',
                'video_verification_path',
                'affiliation_type',
                'affiliation_name',
                'affiliation_contact'
            ]);
        });
    }
};
