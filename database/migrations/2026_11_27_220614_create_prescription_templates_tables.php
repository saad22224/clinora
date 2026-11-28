<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prescription_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name');
            $table->timestamps();

            $table->index(['clinic_id', 'name']);
        });

        Schema::create('prescription_template_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prescription_template_id')->constrained()->cascadeOnDelete();
            $table->string('medicine_name');
            $table->string('dosage')->nullable();
            $table->string('instructions')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prescription_template_items');
        Schema::dropIfExists('prescription_templates');
    }
};
