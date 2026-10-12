<?php

namespace App\Console\Commands;

use App\Support\Qa\QaDatasetBuilder;
use App\Support\Qa\QaDatasetCleanup;
use App\Support\Qa\QaEnvironmentGuard;
use App\Support\Qa\QaManifest;
use Illuminate\Console\Command;
use Throwable;

class QaGenerateCommand extends Command
{
    protected $signature = 'clinora:qa-generate
                            {--dry-run : Preview planned generation with zero writes}
                            {--force : Required for actual database/file writes}
                            {--no-replace : Do not cleanup the latest QA run before generating}
                            {--profile=standard : Dataset profile (small|standard|edge)}
                            {--seed= : Optional RNG seed for reproducible fakes}';

    protected $description = 'Generate the Clinora QA dataset into the isolated clinora_qa database';

    public function handle(
        QaEnvironmentGuard $guard,
        QaManifest $manifest,
        QaDatasetBuilder $builder,
        QaDatasetCleanup $cleanup,
    ): int {
        $dryRun = (bool) $this->option('dry-run');
        $force = (bool) $this->option('force');
        $profile = (string) $this->option('profile');
        $seed = $this->option('seed') !== null ? (int) $this->option('seed') : null;
        $replace = ! $this->option('no-replace');

        try {
            $guard->assertIntegrationsRemainDisabled();

            if ($dryRun) {
                $guard->assertSafeForDryRun();
                $this->printDryRunPlan($manifest, $profile, $seed);

                return self::SUCCESS;
            }

            $guard->assertSafeForWrites($force);

            if ($seed !== null) {
                mt_srand($seed);
            }

            if ($replace) {
                $latest = $manifest->latestRunId();
                if ($latest) {
                    $this->info("Replacing previous QA run [{$latest}]…");
                    $cleanup->cleanupRun($latest, dryRun: false);
                }
            }

            $skeleton = $manifest->makeSkeleton($profile, $seed, [
                'dry_run' => false,
            ]);
            $runId = (string) $skeleton['run_id'];

            $built = $builder->build($runId, $profile, writeFiles: true);
            $payload = array_merge($skeleton, $built, [
                'dry_run' => false,
                'meta' => array_merge($skeleton['meta'] ?? [], $built['meta'] ?? []),
            ]);

            $path = $manifest->write($payload);

            $this->info("QA dataset generated. run_id={$runId}");
            $this->line('Manifest: '.$path);
            $this->line('Accounts: '.count($payload['accounts'] ?? []));
            $this->line('Clinics: '.count($payload['records']['clinics'] ?? []));

            return self::SUCCESS;
        } catch (Throwable $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }
    }

    private function printDryRunPlan(QaManifest $manifest, string $profile, ?int $seed): void
    {
        $summary = app(QaEnvironmentGuard::class)->targetSummary();
        $planned = app(\App\Support\Qa\QaAccountCatalog::class)->plannedAccounts();

        $this->info('QA generate dry-run');
        $this->table(
            ['Key', 'Value'],
            [
                ['profile', $profile],
                ['seed', $seed === null ? '(none)' : (string) $seed],
                ['app_env', $summary['app_env']],
                ['database', $summary['database']],
                ['connection', $summary['connection']],
                ['planned_accounts', (string) count($planned)],
            ]
        );

        $this->line('Planned primary account: qa+org-owner-a@clinora.test');
        $this->info('Dry-run completed with zero writes');
    }
}
