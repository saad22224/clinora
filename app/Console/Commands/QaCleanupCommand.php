<?php

namespace App\Console\Commands;

use App\Support\Qa\QaDatasetCleanup;
use App\Support\Qa\QaEnvironmentGuard;
use Illuminate\Console\Command;
use Throwable;

class QaCleanupCommand extends Command
{
    protected $signature = 'clinora:qa-cleanup
                            {--run= : Manifest run id to cleanup}
                            {--dry-run : Preview deletions without writing}
                            {--force : Required for actual deletions}';

    protected $description = 'Remove QA dataset records and private files for a specific run id';

    public function handle(QaEnvironmentGuard $guard, QaDatasetCleanup $cleanup): int
    {
        $runId = $this->option('run');
        $dryRun = (bool) $this->option('dry-run');
        $force = (bool) $this->option('force');

        if (! is_string($runId) || trim($runId) === '') {
            $this->error('Cleanup requires --run=<run_id> from storage/app/qa-manifests.');

            return self::FAILURE;
        }

        try {
            $guard->assertIntegrationsRemainDisabled();

            if ($dryRun) {
                $guard->assertSafeForDryRun();
                $result = $cleanup->cleanupRun($runId, dryRun: true);
                $this->info("Dry-run cleanup for [{$runId}]");
                $this->line('Would delete files: '.$result['files_deleted']);
                foreach ($result['deleted'] as $type => $count) {
                    if ($count > 0) {
                        $this->line("  {$type}: {$count}");
                    }
                }
                $this->info('Dry-run completed with zero writes');

                return self::SUCCESS;
            }

            $guard->assertSafeForWrites($force);
            $result = $cleanup->cleanupRun($runId, dryRun: false);
            $this->info("Cleaned QA run [{$runId}]");
            $this->line('Files deleted: '.$result['files_deleted']);

            return self::SUCCESS;
        } catch (Throwable $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }
    }
}

