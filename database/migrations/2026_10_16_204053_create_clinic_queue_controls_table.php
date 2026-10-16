<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clinic_queue_controls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id')->constrained()->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('users')->cascadeOnDelete();
            $table->date('queue_date');
            $table->boolean('is_paused')->default(false);
            $table->timestamps();

            $table->unique(['clinic_id', 'doctor_id', 'queue_date'], 'clinic_queue_controls_scope_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clinic_queue_controls');
    }
};
