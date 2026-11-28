<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lab_tests', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable()->index();
            $table->string('name_en');
            $table->string('name_ar')->nullable();
            $table->string('category')->default('general')->index();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('radiology_exams', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable()->index();
            $table->string('name_en');
            $table->string('name_ar')->nullable();
            $table->string('category')->default('general')->index();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('lab_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id')->constrained()->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('visit_id')->constrained('visits')->cascadeOnDelete();
            $table->foreignId('ordered_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status', 32)->default('ordered');
            $table->text('notes')->nullable();
            $table->text('result_notes')->nullable();
            $table->timestamp('ordered_at');
            $table->timestamps();

            $table->index(['clinic_id', 'visit_id']);
        });

        Schema::create('lab_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lab_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lab_test_id')->nullable()->constrained('lab_tests')->nullOnDelete();
            $table->string('name_snapshot');
            $table->string('status', 32)->default('ordered');
            $table->timestamps();
        });

        Schema::create('radiology_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id')->constrained()->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('visit_id')->constrained('visits')->cascadeOnDelete();
            $table->foreignId('ordered_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status', 32)->default('ordered');
            $table->text('notes')->nullable();
            $table->text('result_notes')->nullable();
            $table->timestamp('ordered_at');
            $table->timestamps();

            $table->index(['clinic_id', 'visit_id']);
        });

        Schema::create('radiology_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('radiology_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('radiology_exam_id')->nullable()->constrained('radiology_exams')->nullOnDelete();
            $table->string('name_snapshot');
            $table->string('status', 32)->default('ordered');
            $table->timestamps();
        });

        Schema::table('medical_files', function (Blueprint $table) {
            $table->foreignId('lab_order_id')->nullable()->after('visit_id')->constrained('lab_orders')->nullOnDelete();
            $table->foreignId('radiology_order_id')->nullable()->after('lab_order_id')->constrained('radiology_orders')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('medical_files', function (Blueprint $table) {
            $table->dropConstrainedForeignId('radiology_order_id');
            $table->dropConstrainedForeignId('lab_order_id');
        });

        Schema::dropIfExists('radiology_order_items');
        Schema::dropIfExists('radiology_orders');
        Schema::dropIfExists('lab_order_items');
        Schema::dropIfExists('lab_orders');
        Schema::dropIfExists('radiology_exams');
        Schema::dropIfExists('lab_tests');
    }
};
