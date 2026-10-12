<?php

namespace App\Support\Qa;

/**
 * Canonical QA account identities shared by generate, accounts listing, and tests.
 */
class QaAccountCatalog
{
    public const PASSWORD = 'password';

    /**
     * Deterministic clinic / org slug prefixes owned by the QA dataset.
     */
    public const SLUG_PREFIX = 'qa-';

    /**
     * @return list<array{
     *     key: string,
     *     email: string,
     *     name: string,
     *     role: string,
     *     spatie_role: string|null,
     *     context: string,
     *     notes: string
     * }>
     */
    public function plannedAccounts(): array
    {
        return [
            [
                'key' => 'org_owner_a',
                'email' => 'qa+org-owner-a@clinora.test',
                'name' => 'QA Org Owner A',
                'role' => 'owner',
                'spatie_role' => null,
                'context' => 'organization',
                'notes' => 'Organization owner; clinic_id null; inherits chain subscription',
            ],
            [
                'key' => 'org_restricted',
                'email' => 'qa+org-restricted@clinora.test',
                'name' => 'QA Org Restricted Admin',
                'role' => 'owner',
                'spatie_role' => null,
                'context' => 'organization',
                'notes' => 'Org admin without enter_clinic',
            ],
            [
                'key' => 'populated_owner',
                'email' => 'qa+populated-owner@clinora.test',
                'name' => 'QA Populated Owner',
                'role' => 'owner',
                'spatie_role' => 'clinic_owner',
                'context' => 'clinic:qa-populated-clinic',
                'notes' => 'Standalone populated clinic owner',
            ],
            [
                'key' => 'populated_doctor',
                'email' => 'qa+populated-doctor@clinora.test',
                'name' => 'QA Populated Doctor',
                'role' => 'doctor',
                'spatie_role' => 'doctor',
                'context' => 'clinic:qa-populated-clinic',
                'notes' => 'Full clinic permissions',
            ],
            [
                'key' => 'populated_secretary',
                'email' => 'qa+populated-secretary@clinora.test',
                'name' => 'QA Restricted Secretary',
                'role' => 'secretary',
                'spatie_role' => 'secretary',
                'context' => 'clinic:qa-populated-clinic',
                'notes' => 'Only manage_appointments',
            ],
            [
                'key' => 'empty_owner',
                'email' => 'qa+empty-owner@clinora.test',
                'name' => 'QA Empty Clinic Owner',
                'role' => 'owner',
                'spatie_role' => 'clinic_owner',
                'context' => 'clinic:qa-empty-clinic',
                'notes' => 'Empty clinic (no patients/appointments)',
            ],
            [
                'key' => 'expired_owner',
                'email' => 'qa+expired-owner@clinora.test',
                'name' => 'QA Expired Owner',
                'role' => 'owner',
                'spatie_role' => 'clinic_owner',
                'context' => 'clinic:qa-expired-clinic',
                'notes' => 'Expired subscription clinic',
            ],
            [
                'key' => 'expired_doctor',
                'email' => 'qa+expired-doctor@clinora.test',
                'name' => 'QA Expired Doctor',
                'role' => 'doctor',
                'spatie_role' => 'doctor',
                'context' => 'clinic:qa-expired-clinic',
                'notes' => 'Doctor on expired clinic',
            ],
            [
                'key' => 'peer_owner',
                'email' => 'qa+peer-owner@clinora.test',
                'name' => 'QA Peer Owner',
                'role' => 'owner',
                'spatie_role' => 'clinic_owner',
                'context' => 'clinic:qa-peer-clinic',
                'notes' => 'Cross-tenant peer clinic',
            ],
            [
                'key' => 'peer_doctor',
                'email' => 'qa+peer-doctor@clinora.test',
                'name' => 'QA Peer Doctor',
                'role' => 'doctor',
                'spatie_role' => 'doctor',
                'context' => 'clinic:qa-peer-clinic',
                'notes' => 'Cross-tenant peer doctor',
            ],
            [
                'key' => 'inactive_owner',
                'email' => 'qa+inactive-owner@clinora.test',
                'name' => 'QA Inactive Clinic Owner',
                'role' => 'owner',
                'spatie_role' => 'clinic_owner',
                'context' => 'clinic:qa-inactive-clinic',
                'notes' => 'Clinic is_active=false with active subscription',
            ],
            [
                'key' => 'branch_a_owner',
                'email' => 'qa+branch-a-owner@clinora.test',
                'name' => 'QA Branch A Owner',
                'role' => 'owner',
                'spatie_role' => 'clinic_owner',
                'context' => 'clinic:qa-org-branch-a',
                'notes' => 'Org branch A staff (subscription inherited)',
            ],
            [
                'key' => 'branch_a_doctor',
                'email' => 'qa+branch-a-doctor@clinora.test',
                'name' => 'QA Branch A Doctor',
                'role' => 'doctor',
                'spatie_role' => 'doctor',
                'context' => 'clinic:qa-org-branch-a',
                'notes' => 'Org branch A doctor',
            ],
            [
                'key' => 'branch_b_owner',
                'email' => 'qa+branch-b-owner@clinora.test',
                'name' => 'QA Branch B Owner',
                'role' => 'owner',
                'spatie_role' => 'clinic_owner',
                'context' => 'clinic:qa-org-branch-b',
                'notes' => 'Org branch B staff',
            ],
            [
                'key' => 'branch_b_doctor',
                'email' => 'qa+branch-b-doctor@clinora.test',
                'name' => 'QA Branch B Doctor',
                'role' => 'doctor',
                'spatie_role' => 'doctor',
                'context' => 'clinic:qa-org-branch-b',
                'notes' => 'Org branch B doctor',
            ],
            [
                'key' => 'branch_empty_owner',
                'email' => 'qa+branch-empty-owner@clinora.test',
                'name' => 'QA Branch Empty Owner',
                'role' => 'owner',
                'spatie_role' => 'clinic_owner',
                'context' => 'clinic:qa-org-branch-empty',
                'notes' => 'Empty org branch',
            ],
            [
                'key' => 'qa_superadmin',
                'email' => 'qa+superadmin@clinora.test',
                'name' => 'QA Superadmin',
                'role' => 'superadmin',
                'spatie_role' => 'superadmin',
                'context' => 'platform',
                'notes' => 'Isolated QA superadmin; never uses SUPERADMIN_EMAIL from .env',
            ],
        ];
    }

    /**
     * @return list<string>
     */
    public function emails(): array
    {
        return array_column($this->plannedAccounts(), 'email');
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function keyedAccounts(): array
    {
        $keyed = [];

        foreach ($this->plannedAccounts() as $account) {
            $keyed[$account['key']] = $account;
        }

        return $keyed;
    }
}
