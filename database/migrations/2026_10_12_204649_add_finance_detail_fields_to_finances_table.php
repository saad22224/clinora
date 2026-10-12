<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('finances', function (Blueprint $table) {
            $table->string('category')->nullable()->after('type');
            $table->string('payment_method')->nullable()->after('category');
            $table->foreignId('patient_id')->nullable()->after('clinic_id')->constrained()->nullOnDelete();
            $table->foreignId('appointment_id')->nullable()->after('patient_id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('finances', function (Blueprint $table) {
            $table->dropForeign(['patient_id']);
            $table->dropForeign(['appointment_id']);
            $table->dropColumn(['category', 'payment_method', 'patient_id', 'appointment_id']);
        });
    }
};
