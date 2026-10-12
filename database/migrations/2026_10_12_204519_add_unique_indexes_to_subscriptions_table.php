<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Keep one subscription per clinic before adding a unique index.
        $duplicates = DB::table('subscriptions')
            ->select('clinic_id', DB::raw('MAX(id) as keep_id'))
            ->whereNotNull('clinic_id')
            ->groupBy('clinic_id')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($duplicates as $duplicate) {
            DB::table('subscriptions')
                ->where('clinic_id', $duplicate->clinic_id)
                ->where('id', '!=', $duplicate->keep_id)
                ->delete();
        }

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropForeign(['clinic_id']);
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->unique('clinic_id');
            $table->foreign('clinic_id')->references('id')->on('clinics')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropForeign(['clinic_id']);
            $table->dropUnique(['clinic_id']);
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->foreign('clinic_id')->references('id')->on('clinics')->cascadeOnDelete();
        });
    }
};
