<?php

namespace App\Http\Middleware;

use App\Models\Organization;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOrganizationMember
{
    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(403);
        }

        $organization = $request->route('organization');

        if (! $organization instanceof Organization) {
            $organizationId = $request->route('organization') ?? $request->session()->get('active_organization_id');
            $organization = Organization::query()->find($organizationId);
        }

        if (! $organization) {
            abort(404);
        }

        $membership = $organization->memberships()
            ->where('user_id', $user->id)
            ->first();

        if (! $membership) {
            abort(403, 'ليس لديك صلاحية للوصول إلى هذه المنظمة.');
        }

        $request->attributes->set('organization', $organization);
        $request->attributes->set('organization_membership', $membership);
        app()->instance('organization', $organization);

        return $next($request);
    }
}
