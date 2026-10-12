<?php

namespace Database\Seeders;

use App\Support\Qa\QaDatasetBuilder;
use App\Support\Qa\QaEnvironmentGuard;
use App\Support\Qa\QaManifest;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Seeds the interconnected QA dataset via QaDatasetBuilder.
 *
 * Artisan db:seed may target the local app database when explicitly invoked.
 * `clinora:qa-generate` remains gated by QaEnvironmentGuard (clinora_qa + flags).
 */
class QaDatasetSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->runningUnitTests()) {
            $guard = app(QaEnvironmentGuard::class);
            $guard->assertIntegrationsRemainDisabled();
            // Refuse production / forbidden DB names; allow explicit local seeding (e.g. clinora).
            $guard->assertSafeForDryRun();
        }

        $runId = 'qa_seeder_'.now()->format('Ymd_His').'_'.Str::lower(Str::random(6));
        $writeFiles = ! app()->runningUnitTests();

        $built = app(QaDatasetBuilder::class)->build($runId, 'standard', writeFiles: $writeFiles);

        if (! app()->runningUnitTests()) {
            $manifest = app(QaManifest::class);
            $skeleton = $manifest->makeSkeleton('standard', null, [
                'dry_run' => false,
                'run_id' => $runId,
            ]);
            $manifest->write(array_merge($skeleton, $built, [
                'meta' => array_merge($skeleton['meta'] ?? [], $built['meta'] ?? []),
            ]));
        }
    }
}
