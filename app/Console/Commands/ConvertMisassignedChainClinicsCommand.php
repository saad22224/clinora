<?php

namespace App\Console\Commands;

use App\Models\Clinic;
use App\Models\Organization;
use App\Models\Subscription;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ConvertMisassignedChainClinicsCommand extends Command
{
    protected $signature = 'clinora:convert-misassigned-chain-clinics';

    protected $description = 'Convert standalone clinics assigned a chain-type plan into organizations';

    public function handle(): int
    {
        $subscriptions = Subscription::query()
            ->with(['clinic.users', 'planDetails'])
            ->whereNotNull('clinic_id')
            ->whereNull('organization_id')
            ->whereHas('planDetails', fn ($query) => $query->where('type', 'chain'))
            ->get();

        $convertedCount = 0;

        foreach ($subscriptions as $subscription) {
            $clinic = $subscription->clinic;
            if (! $clinic || $clinic->organization_id !== null) {
                continue;
            }

            DB::transaction(function () use ($clinic, $subscription, &$convertedCount): void {
                $organization = Organization::create([
                    'name' => $clinic->name,
                    'is_active' => (bool) $clinic->is_active,
                ]);

                $clinic->update([
                    'organization_id' => $organization->id,
                ]);

                $subscription->update([
                    'organization_id' => $organization->id,
                    'clinic_id' => null,
                ]);

                $owner = $clinic->users()->where('role', 'owner')->first()
                    ?? $clinic->users()->first();

                if ($owner) {
                    $organization->memberships()->firstOrCreate(
                        ['user_id' => $owner->id],
                        [
                            'role' => 'owner',
                            'permissions' => ['enter_clinic', 'manage_org_clinics', 'view_org_reports'],
                        ]
                    );

                    $owner->update([
                        'clinic_id' => null,
                    ]);
                }

                $convertedCount++;
            });
        }

        $this->info("Converted {$convertedCount} misassigned chain clinic(s) into organizations.");

        return self::SUCCESS;
    }
}

