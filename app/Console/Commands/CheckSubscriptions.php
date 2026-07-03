<?php

namespace App\Console\Commands;

use App\Models\Subscription;
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
    protected $description = 'Check and expire subscriptions that have passed their end date.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $expiredCount = Subscription::where('is_active', true)
            ->where('ends_at', '<', now())
            ->update(['is_active' => false]);

        $this->info("Checked subscriptions. Expired $expiredCount subscriptions.");
    }
}
