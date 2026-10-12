<?php

namespace App\Console\Commands;

use App\Support\Qa\QaAccountCatalog;
use App\Support\Qa\QaManifest;
use Illuminate\Console\Command;

class QaAccountsCommand extends Command
{
    protected $signature = 'clinora:qa-accounts';

    protected $description = 'List QA dataset accounts (from latest manifest, or planned catalog)';

    public function handle(QaAccountCatalog $catalog, QaManifest $manifest): int
    {
        $latest = $manifest->latestRunId();

        if ($latest) {
            $data = $manifest->read($latest);
            $accounts = is_array($data['accounts'] ?? null) ? $data['accounts'] : [];
            $this->info("Accounts from manifest run [{$latest}]");
            $this->renderAccounts($accounts);

            return self::SUCCESS;
        }

        $this->warn('No QA manifest found. Showing planned catalog accounts.');
        $planned = array_map(fn (array $account) => [
            'email' => $account['email'],
            'password' => QaAccountCatalog::PASSWORD,
            'role' => $account['role'],
            'spatie_role' => $account['spatie_role'],
            'context' => $account['context'],
            'notes' => $account['notes'],
        ], $catalog->plannedAccounts());

        $this->renderAccounts($planned);

        return self::SUCCESS;
    }

    /**
     * @param  list<array<string, mixed>>  $accounts
     */
    private function renderAccounts(array $accounts): void
    {
        $rows = [];

        foreach ($accounts as $account) {
            $rows[] = [
                $account['email'] ?? '',
                $account['password'] ?? QaAccountCatalog::PASSWORD,
                $account['role'] ?? '',
                $account['spatie_role'] ?? '',
                $account['context'] ?? '',
            ];
        }

        $this->table(['Email', 'Password', 'Role', 'Spatie', 'Context'], $rows);
    }
}
