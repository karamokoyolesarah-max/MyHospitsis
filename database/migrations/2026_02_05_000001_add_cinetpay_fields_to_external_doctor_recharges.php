<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('external_doctor_recharges', function (Blueprint $table) {
            // CinetPay integration fields
            if (!Schema::hasColumn('external_doctor_recharges', 'cinetpay_transaction_id')) {
                $table->string('cinetpay_transaction_id')->nullable();
            }
            if (!Schema::hasColumn('external_doctor_recharges', 'payment_token')) {
                $table->string('payment_token')->nullable();
            }
            if (!Schema::hasColumn('external_doctor_recharges', 'cinetpay_response')) {
                $table->json('cinetpay_response')->nullable();
            }
            
            // Failure handling
            if (!Schema::hasColumn('external_doctor_recharges', 'failure_reason')) {
                $table->string('failure_reason')->nullable();
            }
            
            // SMS tracking
            if (!Schema::hasColumn('external_doctor_recharges', 'sms_sent_at')) {
                $table->timestamp('sms_sent_at')->nullable();
            }
            
            // Wave manual validation
            if (!Schema::hasColumn('external_doctor_recharges', 'requires_manual_validation')) {
                $table->boolean('requires_manual_validation')->default(false);
            }
            if (!Schema::hasColumn('external_doctor_recharges', 'validated_by')) {
                $table->unsignedBigInteger('validated_by')->nullable();
            }
            if (!Schema::hasColumn('external_doctor_recharges', 'validated_at')) {
                $table->timestamp('validated_at')->nullable();
            }
            if (!Schema::hasColumn('external_doctor_recharges', 'validation_notes')) {
                $table->text('validation_notes')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('external_doctor_recharges', function (Blueprint $table) {
            $columnsToDrop = [
                'cinetpay_transaction_id',
                'payment_token',
                'cinetpay_response',
                'failure_reason',
                'sms_sent_at',
                'requires_manual_validation',
                'validated_by',
                'validated_at',
                'validation_notes',
            ];
            
            foreach ($columnsToDrop as $column) {
                if (Schema::hasColumn('external_doctor_recharges', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
