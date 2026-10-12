<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clinics', function (Blueprint $table) {
            $table->foreignId('organization_id')->nullable()->after('id')->constrained()->nullOnDelete();
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->foreignId('organization_id')->nullable()->after('clinic_id')->constrained()->cascadeOnDelete();
            $table->unique('organization_id');
        });

        Schema::table('plans', function (Blueprint $table) {
            $table->string('type')->default('single')->after('name'); // single|chain
            $table->text('description')->nullable()->after('type');
            $table->unsignedInteger('max_clinics')->default(1)->after('duration_in_days');
        });

        // Allow clinic_id to be null for organization-scoped subscriptions.
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropForeign(['clinic_id']);
        });

        $driver = Schema::getConnection()->getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            DB::statement('ALTER TABLE subscriptions MODIFY clinic_id BIGINT UNSIGNED NULL');
        } else {
            Schema::table('subscriptions', function (Blueprint $table) {
                $table->unsignedBigInteger('clinic_id')->nullable()->change();
            });
        }

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->foreign('clinic_id')->references('id')->on('clinics')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn(['type', 'description', 'max_clinics']);
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropForeign(['organization_id']);
            $table->dropUnique(['organization_id']);
            $table->dropColumn('organization_id');
        });

        Schema::table('clinics', function (Blueprint $table) {
            $table->dropForeign(['organization_id']);
            $table->dropColumn('organization_id');
        });
    }
};
