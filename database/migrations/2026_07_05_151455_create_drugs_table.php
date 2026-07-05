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
        Schema::create('drugs', function (Blueprint $table) {
            $table->id();
            $table->string('commercial_name_en', 255)->index();
            $table->string('commercial_name_ar', 255)->nullable()->index();
            $table->text('scientific_name')->nullable();
            $table->string('manufacturer', 255)->nullable();
            $table->string('drug_class', 255)->nullable();
            $table->string('route', 100)->nullable();
            $table->decimal('price_egp', 12, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drugs');
    }
};
