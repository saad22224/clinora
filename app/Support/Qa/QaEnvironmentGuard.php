<?php

namespace App\Support\Qa;

use RuntimeException;

class QaEnvironmentGuard
{
    /**
     * @var list<string>
     */
    private const FORBIDDEN_ENVIRONMENTS = [
        'production',
        'prod',
    ];

    /**
     * @var list<string>
     */
    private const FORBIDDEN_DATABASE_NAMES = [
        'production',
        'prod',
        'clinora_prod',
        'clinora-prod',
        'clinora_production',
        'clinora-production',
        'live',
    ];

    /**
     * @return array{
     *     app_env: string,
     *     connection: string,
     *     database: string,
     *     driver: string,
     *     allowed_qa_database: string,
     *     qa_allow_writes_flag: bool,
     *     ai_enabled: bool,
     *     whatsapp_enabled: bool
     * }
     */
    public function targetSummary(): array
    {
        $connection = (string) config('database.default');

        return [
            'app_env' => $this->currentEnvironment(),
            'connection' => $connection,
            'database' => $this->databaseName($connection),
            'driver' => (string) config("database.connections.{$connection}.driver", 'unknown'),
            'allowed_qa_database' => (string) config('clinora.qa.allowed_database', 'clinora_qa'),
            'qa_allow_writes_flag' => (bool) config('clinora.qa.allow_writes', false),
            'ai_enabled' => (bool) config('clinora.features.ai_enabled', false),
            'whatsapp_enabled' => (bool) config('clinora.features.whatsapp_enabled', false),
        ];
    }

    /**
     * Dry-run may inspect configuration but must never write.
     */
    public function assertSafeForDryRun(): void
    {
        $this->assertNotProductionEnvironment();
        $this->assertDatabaseNotForbidden();
    }

    /**
     * Actual generation/cleanup writes require an isolated QA database.
     */
    public function assertSafeForWrites(bool $force): void
    {
        $this->assertSafeForDryRun();

        if (! $force) {
            throw new RuntimeException(
                'Writing QA data requires the explicit --force flag. Use --dry-run to preview safely.'
            );
        }

        if (! (bool) config('clinora.qa.allow_writes', false)) {
            throw new RuntimeException(
                'QA writes are disabled. Set CLINORA_QA_ALLOW=true in the environment that points at clinora_qa (do not change production .env).'
            );
        }

        $summary = $this->targetSummary();
        $allowed = strtolower($summary['allowed_qa_database']);
        $current = strtolower($summary['database']);

        if ($current === '' || $current !== $allowed) {
            throw new RuntimeException(
                "QA writes are only allowed when the active database is [{$summary['allowed_qa_database']}]. ".
                "Current database is [{$summary['database']}] on connection [{$summary['connection']}]. ".
                'Configure an isolated clinora_qa connection/database first; do not write to the current app database.'
            );
        }
    }

    public function assertIntegrationsRemainDisabled(): void
    {
        if ((bool) config('clinora.features.ai_enabled', false)) {
            throw new RuntimeException('Refuse to run QA factory while CLINORA_AI_ENABLED is true.');
        }

        if ((bool) config('clinora.features.whatsapp_enabled', false)) {
            throw new RuntimeException('Refuse to run QA factory while CLINORA_WHATSAPP_ENABLED is true.');
        }
    }

    private function assertNotProductionEnvironment(): void
    {
        $env = $this->currentEnvironment();

        if (in_array($env, self::FORBIDDEN_ENVIRONMENTS, true) || app()->isProduction()) {
            throw new RuntimeException(
                "QA factory refused to run in environment [{$env}]."
            );
        }
    }

    private function currentEnvironment(): string
    {
        return strtolower((string) config('app.env', app()->environment()));
    }

    private function assertDatabaseNotForbidden(): void
    {
        $database = strtolower($this->databaseName((string) config('database.default')));

        if ($database !== '' && in_array($database, self::FORBIDDEN_DATABASE_NAMES, true)) {
            throw new RuntimeException(
                "QA factory refused to target forbidden database [{$database}]."
            );
        }
    }

    private function databaseName(string $connection): string
    {
        $database = config("database.connections.{$connection}.database");

        if (is_string($database)) {
            return $database;
        }

        return '';
    }
}
