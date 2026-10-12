<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\AuditLog;
use App\Models\Clinic;
use App\Models\Organization;
use App\Models\Scopes\TenantScope;
use App\Models\User;
use App\Services\FinanceReportService;
use App\Services\SubscriptionService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class OrganizationClinicController extends Controller
{
    public function __construct(
        protected SubscriptionService $subscriptionService,
        protected FinanceReportService $financeReportService,
    ) {}

    public function show(Request $request, Organization $organization, Clinic $clinic)
    {
        $organization = $request->attributes->get('organization', $organization);
        $membership = $request->attributes->get('organization_membership');

        abort_if((int) $clinic->organization_id !== (int) $organization->id, 404);
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

        $clinic->loadCount(['patients', 'users']);

        $appointmentsInPeriod = Appointment::query()
            ->withoutGlobalScope(TenantScope::class)
            ->where('clinic_id', $clinic->id)
            ->whereBetween('scheduled_at', [$from->copy()->startOfDay(), $to->copy()->endOfDay()])
            ->count();

        $todayAppointments = Appointment::query()
            ->withoutGlobalScope(TenantScope::class)
            ->where('clinic_id', $clinic->id)
            ->whereDate('scheduled_at', now()->toDateString())
            ->count();

        $finance = $this->financeReportService->forClinic($clinic, $from, $to);

        $recentAppointments = Appointment::query()
            ->withoutGlobalScope(TenantScope::class)
            ->where('clinic_id', $clinic->id)
            ->with('patient:id,name,phone')
            ->latest('scheduled_at')
            ->limit(8)
            ->get(['id', 'patient_id', 'scheduled_at', 'status', 'price', 'payment_status']);

        return view('organization.clinic-show', [
            'organization' => $organization,
            'membership' => $membership,
            'clinic' => $clinic,
            'finance' => $finance,
            'entitlement' => $this->subscriptionService->entitlementSummaryForClinic($clinic),
            'appointmentsInPeriod' => $appointmentsInPeriod,
            'todayAppointments' => $todayAppointments,
            'recentAppointments' => $recentAppointments,
            'date_from' => $from->toDateString(),
            'date_to' => $to->toDateString(),
            'canEnter' => (bool) $membership?->canEnterClinic(),
            'canManage' => (bool) $membership?->canManageClinics(),
            'canViewReports' => (bool) $membership?->canViewReports(),
        ]);
    }

    public function store(Request $request, Organization $organization)
    {
        $organization = $request->attributes->get('organization', $organization);
        $membership = $request->attributes->get('organization_membership');

        abort_unless($membership?->canManageClinics(), 403);
        abort_unless($this->subscriptionService->canCreateClinic($organization), 422, 'تم استهلاك الحد الأقصى لعدد العيادات في الباقة.');

        $validated = $request->validate($this->clinicDetailsRules(creating: true));

        $clinic = DB::transaction(function () use ($organization, $validated) {
            abort_unless(
                $this->subscriptionService->hasActiveAccessForOrganization($organization),
                422,
                'لا يمكن إنشاء عيادة بدون باقة سلسلة نشطة.'
            );

            $clinic = Clinic::create([
                'organization_id' => $organization->id,
                'name' => $validated['clinic_name'],
                'slug' => Clinic::uniqueSlug($validated['clinic_name']),
                'phone' => $validated['phone'] ?? null,
                'address' => $validated['address'] ?? null,
                'is_active' => true,
            ]);

            // Optional branch manager — scoped to this clinic only (not org dashboard).
            if (! empty($validated['create_branch_manager'])) {
                $manager = User::create([
                    'name' => $validated['owner_name'],
                    'email' => $validated['owner_email'],
                    'password' => Hash::make($validated['owner_password']),
                    'clinic_id' => $clinic->id,
                    'role' => 'owner',
                ]);

                Role::firstOrCreate(['name' => 'clinic_owner', 'guard_name' => 'web']);
                $manager->assignRole('clinic_owner');
            }

            return $clinic;
        });

        AuditLog::record('organization.clinic_created', $clinic, [
            'organization_id' => $organization->id,
            'entitlement' => $this->subscriptionService->entitlementSummaryForClinic($clinic),
        ]);

        return redirect()->route('organization.clinics.show', [$organization, $clinic])
            ->with('success', 'تم إنشاء العيادة ضمن باقة السلسلة — تنتهي مع انتهاء باقة المنظمة.');
    }

    public function update(Request $request, Organization $organization, Clinic $clinic)
    {
        $organization = $request->attributes->get('organization', $organization);
        $membership = $request->attributes->get('organization_membership');

        abort_unless($membership?->canManageClinics(), 403);
        abort_if((int) $clinic->organization_id !== (int) $organization->id, 404);

        $validated = $request->validate($this->clinicDetailsRules(creating: false));

        $clinic->update([
            'name' => $validated['clinic_name'],
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'is_active' => $request->boolean('is_active'),
        ]);

        AuditLog::record('organization.clinic_updated', $clinic, [
            'organization_id' => $organization->id,
        ]);

        return redirect()->route('organization.clinics.show', [$organization, $clinic])
            ->with('success', 'تم تحديث بيانات العيادة بنجاح.');
    }

    /**
     * @return array<string, mixed>
     */
    private function clinicDetailsRules(bool $creating): array
    {
        $rules = [
            'clinic_name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:255'],
        ];

        if ($creating) {
            $rules['create_branch_manager'] = ['nullable', 'boolean'];
            $rules['owner_name'] = ['required_if:create_branch_manager,1', 'nullable', 'string', 'max:255'];
            $rules['owner_email'] = ['required_if:create_branch_manager,1', 'nullable', 'email', 'unique:users,email'];
            $rules['owner_password'] = ['required_if:create_branch_manager,1', 'nullable', 'string', 'min:8'];
        } else {
            $rules['is_active'] = ['nullable', 'boolean'];
        }

        return $rules;
    }

    public function destroy(Request $request, Organization $organization, Clinic $clinic)
    {
        $organization = $request->attributes->get('organization', $organization);
        $membership = $request->attributes->get('organization_membership');

        abort_unless($membership?->canManageClinics(), 403);
        abort_if((int) $clinic->organization_id !== (int) $organization->id, 404);

        DB::transaction(function () use ($request, $clinic) {
            if ((int) $request->session()->get('active_clinic_id') === (int) $clinic->id) {
                $request->session()->forget('active_clinic_id');
            }

            $clinic->update(['is_active' => false]);
            $clinic->delete();
        });

        AuditLog::record('organization.clinic_deleted', $organization, [
            'clinic_id' => $clinic->id,
            'clinic_name' => $clinic->name,
        ]);

        return redirect()->route('organization.dashboard', $organization)
            ->with('success', 'تم حذف العيادة وأرشفتها. يمكن استرجاعها لاحقاً عند الحاجة.');
    }
}
