<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clinics', function (Blueprint $table) {
            $table->string('prescription_layout', 32)->default('classic')->after('social_links');
            $table->string('prescription_print_locale', 5)->default('ar')->after('prescription_layout');
        });
    }

    public function down(): void
    {
        Schema::table('clinics', function (Blueprint $table) {
            $table->dropColumn(['prescription_layout', 'prescription_print_locale']);
        });
    }
};
