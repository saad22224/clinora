<?php

namespace App\Services;

use App\Repositories\Interfaces\PatientRepositoryInterface;
use Exception;

class PatientService
{
    public function __construct(
        protected PatientRepositoryInterface $patientRepo,
        protected SubscriptionService $subscriptionService
    ) {}

    public function registerPatient(array $data)
    {
        if (! $this->subscriptionService->hasActiveSubscription()) {
            throw new Exception('Your subscription is inactive. Please contact support.');
        }

        return $this->patientRepo->create($data);
    }
}
