<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id')->constrained()->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('appointment_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('doctor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('visited_at');
            $table->string('status', 32)->default('open');
            $table->text('chief_complaint')->nullable();
            $table->text('clinical_notes')->nullable();
            $table->timestamps();

            $table->index(['clinic_id', 'patient_id', 'visited_at']);
        });

        Schema::table('patient_diagnoses', function (Blueprint $table) {
            $table->foreignId('visit_id')->nullable()->after('patient_id')->constrained('visits')->nullOnDelete();
        });

        Schema::table('prescriptions', function (Blueprint $table) {
            $table->foreignId('visit_id')->nullable()->after('appointment_id')->constrained('visits')->nullOnDelete();
        });

        Schema::table('medical_files', function (Blueprint $table) {
            $table->foreignId('visit_id')->nullable()->after('patient_id')->constrained('visits')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('medical_files', function (Blueprint $table) {
            $table->dropConstrainedForeignId('visit_id');
        });

        Schema::table('prescriptions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('visit_id');
        });

        Schema::table('patient_diagnoses', function (Blueprint $table) {
            $table->dropConstrainedForeignId('visit_id');
        });

        Schema::dropIfExists('visits');
    }
};
