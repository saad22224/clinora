<?php

namespace App\Models;

use Database\Factories\OrganizationUserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrganizationUser extends Model
{
    /** @use HasFactory<OrganizationUserFactory> */
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'user_id',
        'role',
        'permissions',
    ];

    protected function casts(): array
    {
        return [
            'permissions' => 'array',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isOwner(): bool
    {
        return $this->role === 'owner';
    }

    public function canEnterClinic(): bool
    {
        if ($this->isOwner()) {
            return true;
        }

        $permissions = $this->permissions ?? [];

        return in_array('enter_clinic', $permissions, true);
    }

    public function canManageClinics(): bool
    {
        if ($this->isOwner()) {
            return true;
        }

        $permissions = $this->permissions ?? [];

        return in_array('manage_org_clinics', $permissions, true);
    }

    public function canViewReports(): bool
    {
        if ($this->isOwner()) {
            return true;
        }

        $permissions = $this->permissions ?? [];

        return in_array('view_org_reports', $permissions, true);
    }
}
