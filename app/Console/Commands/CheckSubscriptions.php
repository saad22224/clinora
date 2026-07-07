<?php

namespace App\Console\Commands;

use App\Services\SubscriptionService;
use Illuminate\Console\Command;

class CheckSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check and expire subscriptions that have passed their end date or whose plans are inactive.';

    /**
     * Execute the console command.
     */
    public function handle(SubscriptionService $subscriptionService): void
    {
        $expiredCount = $subscriptionService->processExpirations();

        $this->info("Checked subscriptions. Expired {$expiredCount} subscriptions.");
    }
}
