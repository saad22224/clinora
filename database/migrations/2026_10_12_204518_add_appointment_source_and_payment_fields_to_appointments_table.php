<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->string('source')->nullable()->after('status')->index();
            $table->string('payment_status')->default('unpaid')->after('price');
            $table->decimal('amount_paid', 10, 2)->default(0)->after('payment_status');
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn(['source', 'payment_status', 'amount_paid']);
        });
    }
};
