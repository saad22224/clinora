<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Services\FinanceReportService;
use App\Services\SubscriptionService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct(
        protected FinanceReportService $financeReportService,
        protected SubscriptionService $subscriptionService
    ) {}

    public function index(Request $request, Organization $organization)
    {
        $organization = $request->attributes->get('organization', $organization);
        $membership = $request->attributes->get('organization_membership');

        abort_unless($membership?->canViewReports(), 403, 'ليس لديك صلاحية عرض تقارير المنظمة.');
        abort_unless($this->subscriptionService->hasActiveAccessForOrganization($organization), 403);

        $validated = $request->validate([
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date'],
        ]);

        $from = isset($validated['date_from'])
            ? Carbon::parse($validated['date_from'])->startOfDay()
            : now()->startOfMonth();
        $to = isset($validated['date_to'])
            ? Carbon::parse($validated['date_to'])->endOfDay()
            : now()->endOfDay();

        $report = $this->financeReportService->forOrganization($organization, $from, $to);

        return view('organization.reports', [
            'organization' => $organization,
            'membership' => $membership,
            'totals' => $report['totals'],
            'branches' => $report['branches'],
            'date_from' => $from->toDateString(),
            'date_to' => $to->toDateString(),
        ]);
    }
}
