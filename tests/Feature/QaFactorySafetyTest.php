<?php

use App\Models\User;
use App\Support\Qa\QaEnvironmentGuard;
use App\Support\Qa\QaManifest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

uses(RefreshDatabase::class);

beforeEach(function () {
    config([
        'clinora.features.ai_enabled' => false,
        'clinora.features.whatsapp_enabled' => false,
        'clinora.qa.allow_writes' => false,
        'clinora.qa.allowed_database' => 'clinora_qa',
    ]);
});

test('dry-run generate succeeds and performs no user writes', function () {
    $beforeUsers = User::query()->count();
    $beforeManifestFiles = count(File::glob(storage_path('app/qa-manifests/*.json')) ?: []);

    $exit = Artisan::call('clinora:qa-generate', [
        '--dry-run' => true,
        '--profile' => 'standard',
        '--seed' => 42,
    ]);

    expect($exit)->toBe(0);
    expect(User::query()->count())->toBe($beforeUsers);
    expect(count(File::glob(storage_path('app/qa-manifests/*.json')) ?: []))->toBe($beforeManifestFiles);
    expect(Artisan::output())->toContain('Dry-run completed with zero writes');
});

test('generate refuses production environment', function () {
    config(['app.env' => 'production']);

    $exit = Artisan::call('clinora:qa-generate', [
        '--dry-run' => true,
        '--profile' => 'small',
    ]);

    expect($exit)->toBe(1);
    expect(Artisan::output())->toContain('refused to run in environment');
});

test('generate refuses when AI integration is enabled', function () {
    config(['clinora.features.ai_enabled' => true]);

    $exit = Artisan::call('clinora:qa-generate', [
        '--dry-run' => true,
        '--profile' => 'small',
    ]);

    expect($exit)->toBe(1);
    expect(Artisan::output())->toContain('CLINORA_AI_ENABLED');
});

test('generate refuses when WhatsApp integration is enabled', function () {
    config(['clinora.features.whatsapp_enabled' => true]);

    $exit = Artisan::call('clinora:qa-generate', [
        '--dry-run' => true,
        '--profile' => 'small',
    ]);

    expect($exit)->toBe(1);
    expect(Artisan::output())->toContain('CLINORA_WHATSAPP_ENABLED');
});

test('write mode refuses without force and without qa allow flag', function () {
    $exit = Artisan::call('clinora:qa-generate', [
        '--profile' => 'small',
    ]);

    expect($exit)->toBe(1);
    expect(Artisan::output())->toContain('--force');
});

test('write mode refuses non clinora_qa database even with force and allow flag', function () {
    config([
        'clinora.qa.allow_writes' => true,
        'database.default' => 'sqlite',
        'database.connections.sqlite.database' => ':memory:',
    ]);

    $exit = Artisan::call('clinora:qa-generate', [
        '--profile' => 'small',
        '--force' => true,
    ]);

    expect($exit)->toBe(1);
    expect(Artisan::output())->toContain('clinora_qa');
});

test('write mode refuses forbidden database names', function () {
    config([
        'database.default' => 'sqlite',
        'database.connections.sqlite.database' => 'clinora_production',
    ]);

    $exit = Artisan::call('clinora:qa-generate', [
        '--dry-run' => true,
        '--profile' => 'small',
    ]);

    expect($exit)->toBe(1);
    expect(Artisan::output())->toContain('forbidden database');
});

test('environment guard exposes target summary without secrets', function () {
    $summary = app(QaEnvironmentGuard::class)->targetSummary();

    expect($summary)->toHaveKeys([
        'app_env',
        'connection',
        'database',
        'driver',
        'allowed_qa_database',
        'qa_allow_writes_flag',
        'ai_enabled',
        'whatsapp_enabled',
    ]);
    expect($summary)->not->toHaveKey('password');
    expect($summary)->not->toHaveKey('username');
});

test('manifest skeleton can be written and read in storage without touching business tables', function () {
    $manifest = app(QaManifest::class);
    $skeleton = $manifest->makeSkeleton('edge', 7, ['dry_run' => false]);
    $path = $manifest->write($skeleton);

    expect(File::exists($path))->toBeTrue();
    expect($manifest->read($skeleton['run_id'])['profile'])->toBe('edge');
    expect($manifest->latestRunId())->toBe($skeleton['run_id']);

    File::delete($path);
    expect(User::query()->count())->toBe(0);
});

test('cleanup without run id fails safely', function () {
    $exit = Artisan::call('clinora:qa-cleanup', [
        '--dry-run' => true,
    ]);

    expect($exit)->toBe(1);
    expect(Artisan::output())->toContain('--run=');
});

test('accounts command lists planned accounts when no manifest exists', function () {
    $dir = storage_path('app/qa-manifests');
    if (File::isDirectory($dir)) {
        foreach (File::files($dir) as $file) {
            File::delete($file->getPathname());
        }
    }

    $exit = Artisan::call('clinora:qa-accounts');

    expect($exit)->toBe(0);
    expect(Artisan::output())->toContain('qa+org-owner-a@clinora.test');
});
