<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clinics', function (Blueprint $table) {
            $table->text('doctor_bio')->nullable()->after('description');
            $table->string('doctor_title')->nullable()->after('doctor_bio'); // e.g. "طبيب أسنان"
            $table->string('doctor_image')->nullable()->after('doctor_title');
            $table->string('phone')->nullable()->after('doctor_image');
            $table->string('address')->nullable()->after('phone');
            $table->json('working_hours')->nullable()->after('address');
            // working_hours format: {"saturday":{"open":"09:00","close":"17:00","enabled":true}, ...}
        });
    }

    public function down(): void
    {
        Schema::table('clinics', function (Blueprint $table) {
            $table->dropColumn(['doctor_bio', 'doctor_title', 'doctor_image', 'phone', 'address', 'working_hours']);
        });
    }
};
