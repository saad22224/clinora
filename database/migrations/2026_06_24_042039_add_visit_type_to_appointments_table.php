<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->foreignId('visit_type_id')->nullable()->after('doctor_id')->constrained('visit_types')->nullOnDelete();
            $table->decimal('price', 10, 2)->nullable()->after('visit_type_id');
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropForeign(['visit_type_id']);
            $table->dropColumn(['visit_type_id', 'price']);
        });
    }
};
