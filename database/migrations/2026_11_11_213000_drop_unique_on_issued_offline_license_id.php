<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('issued_offline_licenses')) {
            return;
        }

        $unique = collect(Schema::getIndexes('issued_offline_licenses'))
            ->first(fn (array $index): bool => ($index['unique'] ?? false)
                && ($index['columns'] ?? []) === ['license_id']);

        if ($unique === null) {
            return;
        }

        Schema::table('issued_offline_licenses', function (Blueprint $table) use ($unique) {
            $table->dropUnique($unique['name'] ?? ['license_id']);
        });
    }

    public function down(): void
    {
        Schema::table('issued_offline_licenses', function (Blueprint $table) {
            $table->unique('license_id');
        });
    }
};
