<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clinics', function (Blueprint $table) {
            $table->string('hero_title')->nullable()->after('name');
            $table->string('hero_subtitle')->nullable()->after('hero_title');
            $table->text('about_us')->nullable()->after('description');
            $table->json('social_links')->nullable()->after('working_hours');
        });
    }

    public function down(): void
    {
        Schema::table('clinics', function (Blueprint $table) {
            $table->dropColumn(['hero_title', 'hero_subtitle', 'about_us', 'social_links']);
        });
    }
};
