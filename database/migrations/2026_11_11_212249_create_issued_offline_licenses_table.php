<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('issued_offline_licenses', function (Blueprint $table) {
            $table->id();
            // Same license_id may appear across renewals (Offline older_license rule); history is preserved per row.
            $table->string('license_id', 64)->index();
            $table->string('clinic_name');
            $table->string('customer_name')->nullable();
            $table->string('key_id', 64);
            $table->json('fingerprint_components');
            $table->string('fingerprint_digest', 64)->index();
            $table->unsignedTinyInteger('min_match')->default(3);
            $table->text('install_public_key')->nullable();
            $table->timestamp('license_issued_at');
            $table->timestamp('starts_at');
            $table->timestamp('expires_at')->index();
            $table->unsignedSmallInteger('grace_days')->default(30);
            $table->unsignedSmallInteger('max_devices')->default(5);
            $table->unsignedSmallInteger('max_doctors')->nullable();
            $table->unsignedInteger('transfer_seq')->default(0);
            $table->json('features')->nullable();
            $table->longText('envelope_json');
            $table->string('envelope_sha256', 64);
            $table->string('lifecycle_status', 32)->default('issued')->index();
            $table->foreignId('renewed_from_id')->nullable()->constrained('issued_offline_licenses')->nullOnDelete();
            $table->foreignId('issued_by')->nullable()->constrained('users')->nullOnDelete();
            $table->json('request_meta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('issued_offline_licenses');
    }
};
