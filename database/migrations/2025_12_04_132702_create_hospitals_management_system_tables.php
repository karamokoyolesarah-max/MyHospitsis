<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    { // 1. Table des Hôpitaux (La racine)
        Schema::create('hospitals', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug', 191)->unique();
            $table->string('address')->nullable();
            $table->timestamps();
        });

        // 2. Table des services
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hospital_id')->constrained()->cascadeOnDelete(); 
            $table->string('name');
            $table->string('code', 191)->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 3. Modification de la table Users
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'hospital_id')) {
                $table->foreignId('hospital_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
            }
            if (!Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['admin', 'doctor', 'nurse', 'administrative'])->default('administrative')->after('password');
            }
            if (!Schema::hasColumn('users', 'service_id')) {
                $table->foreignId('service_id')->nullable()->after('role')->constrained()->nullOnDelete();
            }
            // ... (tes autres colonnes phone, registration, etc.)
            if (!Schema::hasColumn('users', 'is_active')) $table->boolean('is_active')->default(true);
            if (!Schema::hasColumn('users', 'phone')) $table->string('phone')->nullable();
            if (!Schema::hasColumn('users', 'registration_number')) $table->string('registration_number')->nullable();
            if (!Schema::hasColumn('users', 'mfa_enabled')) $table->boolean('mfa_enabled')->default(false);
            if (!Schema::hasColumn('users', 'mfa_secret')) $table->string('mfa_secret')->nullable();
            if (!Schema::hasColumn('users', 'deleted_at')) $table->softDeletes();
        });

        // 4. Table des patients
        Schema::create('patients', function (Blueprint $table) {
             $table->id();
            $table->foreignId('hospital_id')->constrained()->cascadeOnDelete(); // AJOUT ICI
            $table->string('ipu', 191)->unique();
            $table->string('name');
            $table->string('first_name');
            $table->date('dob')->comment('Date of Birth');
            $table->enum('gender', ['Homme', 'Femme', 'Other']);
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->string('blood_group')->nullable();
            $table->json('allergies')->nullable();
            $table->text('medical_history')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // 5. Table des chambres
        Schema::create('rooms', function (Blueprint $table) {
    $table->id();
    $table->foreignId('hospital_id')->constrained()->cascadeOnDelete();
     $table->string('room_number');
            $table->integer('bed_capacity')->default(1);
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['available', 'occupied', 'cleaning', 'maintenance'])->default('available');
            $table->string('type')->nullable()->comment('standard, VIP, isolation');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
});
        // 6. Table des admissions
        Schema::create('admissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hospital_id')->constrained()->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('room_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('doctor_id')->constrained('users')->cascadeOnDelete();
            $table->dateTime('admission_date');
            $table->enum('status', ['active', 'discharged', 'transferred'])->default('active');
            $table->timestamps();
        });

        // 7. Table des rendez-vous
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hospital_id')->constrained()->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('users')->cascadeOnDelete();
            $table->dateTime('appointment_datetime');
            $table->enum('status', ['scheduled', 'confirmed', 'cancelled', 'completed', 'prepared'])->default('scheduled');
            $table->timestamps();
            $table->softDeletes();
            // Dans ta migration create_appointments_table
         $table->foreignId('service_id')->nullable()->constrained()->onDelete('cascade');
        });

        // 8. Table des observations cliniques
        Schema::create('clinical_observations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hospital_id')->constrained()->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['blood_pressure', 'temperature', 'heart_rate', 'weight', 'glucose']);
            $table->string('value');
            $table->timestamps();
        });

        // 9. Table des dossiers médicaux
        Schema::create('medical_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hospital_id')->constrained()->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('recorded_by_id')->constrained('users')->cascadeOnDelete();
            $table->text('content');
            $table->timestamps();
        });

        // 10. Table des prescriptions
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hospital_id')->constrained()->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('users')->cascadeOnDelete();
            $table->string('medication');
            $table->timestamps();
        });

        // 11. Table des notes de soins
        Schema::create('nursing_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hospital_id')->constrained()->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('nurse_id')->constrained('users')->cascadeOnDelete();
            $table->text('content');
            $table->timestamps();
        });

        // 12. Table des documents médicaux
        Schema::create('medical_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hospital_id')->constrained()->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('file_path');
            $table->timestamps();
        });

        // 13. Table de facturation
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hospital_id')->constrained()->cascadeOnDelete();
            $table->string('invoice_number', 191)->unique();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->decimal('total', 10, 2);
            $table->timestamps();
        });
         
        // Table des lignes de facturation
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->string('description');
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total', 10, 2);
            $table->string('code')->nullable();
            $table->timestamps();
        });
        // ... (suite après les factures)

        // Table des logs d'audit (Liée à l'hôpital pour savoir ce qui s'est passé chez qui)
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hospital_id')->nullable()->constrained()->cascadeOnDelete(); // AJOUTÉ
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action');
            $table->string('resource_type', 100);
            $table->unsignedBigInteger('resource_id')->nullable();
            $table->text('description')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->ipAddress('ip_address');
            $table->string('user_agent')->nullable();
            $table->timestamp('created_at');
            $table->boolean('is_encrypted')->default(false);
            
            $table->index(['hospital_id', 'created_at']); // Index pour filtrer vite par hôpital
            $table->index(['user_id', 'created_at']);
            $table->index(['resource_type', 'resource_id']);
        });

        // Table des alertes cliniques
        Schema::create('clinical_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hospital_id')->constrained()->cascadeOnDelete(); // AJOUTÉ
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('triggered_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('alert_type', ['drug_interaction', 'allergy', 'critical_value', 'prescription_error']);
            $table->enum('severity', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->text('message');
            $table->boolean('is_acknowledged')->default(false);
            $table->foreignId('acknowledged_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('acknowledged_at')->nullable();
            $table->timestamps();
        });

        // Table de disponibilité des médecins
        Schema::create('doctor_availability', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hospital_id')->constrained()->cascadeOnDelete(); // AJOUTÉ
            $table->foreignId('doctor_id')->constrained('users')->cascadeOnDelete();
            $table->enum('day_of_week', ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday']);
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('slot_duration')->default(30);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Table des absences/congés médecins
        Schema::create('doctor_leaves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hospital_id')->constrained()->cascadeOnDelete(); // AJOUTÉ
            $table->foreignId('doctor_id')->constrained('users')->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('leave_type', ['vacation', 'sick', 'conference', 'other'])->default('vacation');
            $table->text('reason')->nullable();
            $table->timestamps();
        });

    }

        public function down(): void
     {
      // 1. Désactiver temporairement les contraintes de clés étrangères 
      // pour éviter les erreurs de "violation de contrainte" lors de la suppression
      Schema::disableForeignKeyConstraints();

     // 2. Liste de toutes tes tables à supprimer
      Schema::dropIfExists('doctor_leaves');
     Schema::dropIfExists('doctor_availability');
     Schema::dropIfExists('clinical_alerts');
     Schema::dropIfExists('audit_logs');
     Schema::dropIfExists('invoice_items');
     Schema::dropIfExists('invoices');
     Schema::dropIfExists('medical_documents');
     Schema::dropIfExists('nursing_notes');
     Schema::dropIfExists('prescriptions');
     Schema::dropIfExists('medical_records');
     Schema::dropIfExists('clinical_observations');
     Schema::dropIfExists('appointments');
     Schema::dropIfExists('admissions');
     Schema::dropIfExists('rooms');
     Schema::dropIfExists('patients');
     Schema::dropIfExists('services');
     Schema::dropIfExists('hospitals'); // On termine par la table racine

    // 3. Nettoyer la table users (on ne la supprime pas, on retire juste nos colonnes)
    Schema::table('users', function (Blueprint $table) {
        // On vérifie si la colonne existe avant de tenter de supprimer la clé étrangère
        if (Schema::hasColumn('users', 'hospital_id')) {
            $table->dropForeign(['hospital_id']);
        }
        if (Schema::hasColumn('users', 'service_id')) {
            $table->dropForeign(['service_id']);
        }

        $table->dropColumn([
            'hospital_id', 
            'role', 
            'service_id', 
            'is_active', 
            'phone', 
            'registration_number', 
            'mfa_enabled', 
            'mfa_secret', 
            'deleted_at'
        ]);
    });

    // 4. Réactiver les contraintes
    Schema::enableForeignKeyConstraints();
}
 };