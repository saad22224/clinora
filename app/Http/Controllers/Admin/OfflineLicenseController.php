<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\IssuedOfflineLicense;
use App\Services\OfflineLicense\LicenseRequestCodec;
use App\Services\OfflineLicense\LicenseSigningKey;
use App\Services\OfflineLicense\OfflineLicenseIssuer;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use InvalidArgumentException;
use RuntimeException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OfflineLicenseController extends Controller
{
    public function index(Request $request): View
    {
        $query = IssuedOfflineLicense::query()->with('issuer')->latest();

        if ($search = trim((string) $request->query('q', ''))) {
            $query->where(function ($builder) use ($search) {
                $builder->where('license_id', 'like', '%'.$search.'%')
                    ->orWhere('clinic_name', 'like', '%'.$search.'%')
                    ->orWhere('customer_name', 'like', '%'.$search.'%');
            });
        }

        $statusFilter = $request->query('status');
        $licenses = $query->paginate(20)->withQueryString();

        if (is_string($statusFilter) && $statusFilter !== '') {
            $licenses->setCollection(
                $licenses->getCollection()->filter(
                    fn (IssuedOfflineLicense $license) => $license->computedStatus() === $statusFilter
                )->values()
            );
        }
        $keyStatus = LicenseSigningKey::status();
        $counts = [
            'total' => IssuedOfflineLicense::query()->count(),
            'active' => IssuedOfflineLicense::query()->where('lifecycle_status', 'issued')->where('starts_at', '<=', now())->where('expires_at', '>', now())->count(),
            'expiring_soon' => IssuedOfflineLicense::query()->where('lifecycle_status', 'issued')->whereBetween('expires_at', [now(), now()->addDays(30)])->count(),
        ];

        return view('admin.offline-licenses.index', compact('licenses', 'keyStatus', 'counts', 'search'));
    }

    public function create(): View
    {
        return view('admin.offline-licenses.create', [
            'keyStatus' => LicenseSigningKey::status(),
        ]);
    }

    public function preview(Request $request): RedirectResponse|View
    {
        $validated = $request->validate([
            'request_code' => ['required', 'string', 'max:20000'],
        ]);

        try {
            $decoded = LicenseRequestCodec::decodeActivation($validated['request_code']);
        } catch (InvalidArgumentException $exception) {
            AuditLog::record('admin.offline_license_request_invalid', null, [
                'message' => $exception->getMessage(),
            ]);

            return back()->withInput()->with('error', $exception->getMessage());
        }

        AuditLog::record('admin.offline_license_request_decoded', null, [
            'fingerprint_digest' => hash('sha256', json_encode($decoded['fingerprint'], JSON_THROW_ON_ERROR)),
            'request_clinic' => $decoded['clinic'],
            'version' => $decoded['version'],
        ]);

        $request->session()->put('offline_license_request', $decoded);

        return view('admin.offline-licenses.issue', [
            'requestData' => $decoded,
            'keyStatus' => LicenseSigningKey::status(),
            'defaults' => [
                'clinic_name' => $decoded['clinic'] ?? '',
                'license_id' => $decoded['license_id'] ?? '',
                'starts_at' => now()->toDateString(),
                'expires_at' => now()->addYear()->toDateString(),
                'grace_days' => (int) config('clinora.offline_license.default_grace_days', 30),
                'max_devices' => (int) config('clinora.offline_license.default_max_devices', 5),
                'min_match' => min((int) config('clinora.offline_license.default_min_match', 3), collect($decoded['fingerprint'])->filter()->count()),
                'transfer_seq' => 0,
            ],
            'renewing' => null,
        ]);
    }

    public function store(Request $request, OfflineLicenseIssuer $issuer): RedirectResponse
    {
        $decoded = $request->session()->get('offline_license_request');

        if (! is_array($decoded)) {
            return redirect()->route('admin.offline-licenses.create')
                ->with('error', 'انتهت جلسة رمز الطلب. الصق الرمز مرة أخرى.');
        }

        $validated = $request->validate([
            'clinic_name' => ['required', 'string', 'max:255'],
            'customer_name' => ['nullable', 'string', 'max:255'],
            'license_id' => ['nullable', 'string', 'max:64', 'regex:/^[A-Za-z0-9._-]*$/'],
            'starts_at' => ['required', 'date'],
            'expires_at' => ['required', 'date', 'after:starts_at'],
            'grace_days' => ['required', 'integer', 'min:0', 'max:365'],
            'max_devices' => ['required', 'integer', 'min:0', 'max:1000'],
            'max_doctors' => ['nullable', 'integer', 'min:0', 'max:1000'],
            'min_match' => ['required', 'integer', 'min:1', 'max:5'],
            'transfer_seq' => ['required', 'integer', 'min:0'],
            'confirm' => ['accepted'],
        ]);

        try {
            $record = $issuer->issue([
                'clinic_name' => $validated['clinic_name'],
                'customer_name' => $validated['customer_name'] ?? null,
                'license_id' => $validated['license_id'] ?: null,
                'starts_at' => CarbonImmutable::parse($validated['starts_at'])->startOfDay(),
                'expires_at' => CarbonImmutable::parse($validated['expires_at'])->endOfDay(),
                'grace_days' => (int) $validated['grace_days'],
                'max_devices' => (int) $validated['max_devices'],
                'max_doctors' => array_key_exists('max_doctors', $validated) && $validated['max_doctors'] !== null && $validated['max_doctors'] !== ''
                    ? (int) $validated['max_doctors']
                    : null,
                'min_match' => (int) $validated['min_match'],
                'transfer_seq' => (int) $validated['transfer_seq'],
                'require_production' => (bool) config('clinora.offline_license.production', true),
            ], $decoded, $request->user());
        } catch (InvalidArgumentException|RuntimeException $exception) {
            AuditLog::record('admin.offline_license_issue_failed', null, [
                'message' => $exception->getMessage(),
            ]);

            return back()->withInput()->with('error', $exception->getMessage());
        }

        $request->session()->forget('offline_license_request');

        return redirect()->route('admin.offline-licenses.show', $record)
            ->with('success', 'تم إصدار الترخيص وتوقيعه. يمكنك تنزيل الملف الآن.');
    }

    public function show(IssuedOfflineLicense $offlineLicense): View
    {
        $offlineLicense->load(['issuer', 'renewedFrom', 'renewals']);

        return view('admin.offline-licenses.show', [
            'license' => $offlineLicense,
            'keyStatus' => LicenseSigningKey::status(),
        ]);
    }

    public function download(IssuedOfflineLicense $offlineLicense): StreamedResponse
    {
        AuditLog::record('admin.offline_license_downloaded', $offlineLicense, [
            'license_id' => $offlineLicense->license_id,
        ]);

        return response()->streamDownload(function () use ($offlineLicense) {
            echo $offlineLicense->envelope_json;
        }, $offlineLicense->downloadFilename(), [
            'Content-Type' => 'application/json; charset=UTF-8',
        ]);
    }

    public function renewForm(IssuedOfflineLicense $offlineLicense): View
    {
        $requestData = [
            'v' => 1,
            'license_id' => $offlineLicense->license_id,
            'clinic' => $offlineLicense->clinic_name,
            'fingerprint' => $offlineLicense->fingerprint_components,
            'install_public_key' => $offlineLicense->install_public_key,
            'version' => $offlineLicense->request_meta['version'] ?? null,
            'high_watermark' => null,
            'generated_at' => null,
        ];

        session(['offline_license_request' => $requestData, 'offline_license_renew_from' => $offlineLicense->id]);

        return view('admin.offline-licenses.issue', [
            'requestData' => $requestData,
            'keyStatus' => LicenseSigningKey::status(),
            'defaults' => [
                'clinic_name' => $offlineLicense->clinic_name,
                'customer_name' => $offlineLicense->customer_name,
                'license_id' => $offlineLicense->license_id,
                'starts_at' => now()->toDateString(),
                'expires_at' => now()->addYear()->toDateString(),
                'grace_days' => $offlineLicense->grace_days,
                'max_devices' => $offlineLicense->max_devices,
                'max_doctors' => $offlineLicense->max_doctors,
                'min_match' => $offlineLicense->min_match,
                'transfer_seq' => $offlineLicense->transfer_seq,
            ],
            'renewing' => $offlineLicense,
        ]);
    }

    public function renew(Request $request, OfflineLicenseIssuer $issuer): RedirectResponse
    {
        $decoded = $request->session()->get('offline_license_request');
        $renewFromId = $request->session()->get('offline_license_renew_from');
        $previous = IssuedOfflineLicense::query()->find($renewFromId);

        if (! is_array($decoded) || $previous === null) {
            return redirect()->route('admin.offline-licenses.index')
                ->with('error', 'انتهت جلسة التجديد. افتح الترخيص مرة أخرى.');
        }

        if ($request->filled('request_code')) {
            try {
                $decoded = LicenseRequestCodec::decodeActivation($request->string('request_code')->toString());
                $request->session()->put('offline_license_request', $decoded);
            } catch (InvalidArgumentException $exception) {
                return back()->withInput()->with('error', $exception->getMessage());
            }
        }

        $validated = $request->validate([
            'clinic_name' => ['required', 'string', 'max:255'],
            'customer_name' => ['nullable', 'string', 'max:255'],
            'license_id' => ['required', 'string', 'max:64', 'regex:/^[A-Za-z0-9._-]+$/'],
            'starts_at' => ['required', 'date'],
            'expires_at' => ['required', 'date', 'after:starts_at'],
            'grace_days' => ['required', 'integer', 'min:0', 'max:365'],
            'max_devices' => ['required', 'integer', 'min:0', 'max:1000'],
            'max_doctors' => ['nullable', 'integer', 'min:0', 'max:1000'],
            'min_match' => ['required', 'integer', 'min:1', 'max:5'],
            'transfer_seq' => ['required', 'integer', 'min:0'],
            'confirm' => ['accepted'],
        ]);

        try {
            $record = $issuer->issue([
                'clinic_name' => $validated['clinic_name'],
                'customer_name' => $validated['customer_name'] ?? null,
                'license_id' => $validated['license_id'],
                'starts_at' => CarbonImmutable::parse($validated['starts_at'])->startOfDay(),
                'expires_at' => CarbonImmutable::parse($validated['expires_at'])->endOfDay(),
                'grace_days' => (int) $validated['grace_days'],
                'max_devices' => (int) $validated['max_devices'],
                'max_doctors' => array_key_exists('max_doctors', $validated) && $validated['max_doctors'] !== null && $validated['max_doctors'] !== ''
                    ? (int) $validated['max_doctors']
                    : null,
                'min_match' => (int) $validated['min_match'],
                'transfer_seq' => (int) $validated['transfer_seq'],
                'require_production' => (bool) config('clinora.offline_license.production', true),
            ], $decoded, $request->user(), $previous);
        } catch (InvalidArgumentException|RuntimeException $exception) {
            return back()->withInput()->with('error', $exception->getMessage());
        }

        $request->session()->forget(['offline_license_request', 'offline_license_renew_from']);

        return redirect()->route('admin.offline-licenses.show', $record)
            ->with('success', 'تم إصدار تجديد موقّع. الملف السابق محفوظ في السجل.');
    }

    public function keys(): View
    {
        return view('admin.offline-licenses.keys', [
            'keyStatus' => LicenseSigningKey::status(),
        ]);
    }
}
