<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->unsignedInteger('queue_number')->nullable()->after('reminder_sent');
            $table->date('queue_date')->nullable()->after('queue_number');
            $table->string('queue_status')->nullable()->after('queue_date');
            $table->timestamp('called_at')->nullable()->after('queue_status');
            $table->timestamp('in_progress_at')->nullable()->after('called_at');
            $table->string('tracking_token', 64)->nullable()->unique()->after('in_progress_at');

            $table->index(['clinic_id', 'doctor_id', 'queue_date']);
            $table->unique(['clinic_id', 'doctor_id', 'queue_date', 'queue_number'], 'appointments_queue_scope_unique');
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropUnique('appointments_queue_scope_unique');
            $table->dropIndex(['clinic_id', 'doctor_id', 'queue_date']);
            $table->dropColumn([
                'queue_number',
                'queue_date',
                'queue_status',
                'called_at',
                'in_progress_at',
                'tracking_token',
            ]);
        });
    }
};
