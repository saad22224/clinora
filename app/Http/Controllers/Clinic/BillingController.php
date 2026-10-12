<?php

namespace App\Http\Controllers\Clinic;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Finance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class BillingController extends Controller
{
    public function index(Request $request)
    {
        $clinic = $this->currentClinic();

        $validated = $request->validate([
            'type' => ['nullable', 'in:income,expense'],
            'search' => ['nullable', 'string', 'max:100'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date'],
        ]);

        $finances = Finance::query()
            ->where('clinic_id', $clinic->id)
            ->with(['patient', 'appointment'])
            ->when($validated['type'] ?? null, fn ($q, $type) => $q->where('type', $type))
            ->when($validated['search'] ?? null, function ($q, $search) {
                $q->where(function ($inner) use ($search) {
                    $inner->where('description', 'like', "%{$search}%")
                        ->orWhere('category', 'like', "%{$search}%");
                });
            })
            ->when($validated['date_from'] ?? null, fn ($q, $from) => $q->whereDate('transaction_date', '>=', $from))
            ->when($validated['date_to'] ?? null, fn ($q, $to) => $q->whereDate('transaction_date', '<=', $to))
            ->latest('transaction_date')
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        $patients = $clinic->patients()->orderBy('name')->get(['id', 'name']);
        $appointments = $clinic->appointments()
            ->with('patient')
            ->where('status', 'completed')
            ->whereIn('payment_status', ['unpaid', 'partial'])
            ->latest('scheduled_at')
            ->limit(50)
            ->get();

        return view('clinic.billing', compact('finances', 'patients', 'appointments'));
    }

    public function store(Request $request)
    {
        $clinic = $this->currentClinic();

        $validated = $request->validate([
            'type' => ['required', Rule::in(['income', 'expense'])],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'description' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:100'],
            'payment_method' => ['nullable', 'string', 'max:100'],
            'transaction_date' => ['required', 'date'],
            'patient_id' => ['nullable', 'exists:patients,id'],
            'appointment_id' => ['nullable', 'exists:appointments,id'],
        ]);

        if (! empty($validated['patient_id'])) {
            abort_if($clinic->patients()->where('id', $validated['patient_id'])->doesntExist(), 403);
        }

        DB::transaction(function () use ($clinic, $validated) {
            $appointment = null;
            if (! empty($validated['appointment_id'])) {
                $appointment = Appointment::query()
                    ->where('clinic_id', $clinic->id)
                    ->findOrFail($validated['appointment_id']);
            }

            Finance::create([
                'clinic_id' => $clinic->id,
                'type' => $validated['type'],
                'amount' => $validated['amount'],
                'description' => $validated['description'],
                'category' => $validated['category'] ?? null,
                'payment_method' => $validated['payment_method'] ?? null,
                'transaction_date' => $validated['transaction_date'],
                'patient_id' => $validated['patient_id'] ?? $appointment?->patient_id,
                'appointment_id' => $appointment?->id,
            ]);

            if ($appointment && $validated['type'] === 'income') {
                $this->syncAppointmentPayment($appointment, (float) $validated['amount']);
            }
        });

        return redirect()->route('clinic.billing.index')
            ->with('success', 'تم تسجيل الحركة المالية بنجاح.');
    }

    public function update(Request $request, Finance $finance)
    {
        abort_if((int) $finance->clinic_id !== $this->currentClinicId(), 403);

        $validated = $request->validate([
            'type' => ['required', Rule::in(['income', 'expense'])],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'description' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:100'],
            'payment_method' => ['nullable', 'string', 'max:100'],
            'transaction_date' => ['required', 'date'],
        ]);

        $finance->update($validated);

        return back()->with('success', 'تم تحديث الحركة المالية.');
    }

    public function destroy(Finance $finance)
    {
        abort_if((int) $finance->clinic_id !== $this->currentClinicId(), 403);
        $finance->delete();

        return back()->with('success', 'تم حذف الحركة المالية.');
    }

    private function syncAppointmentPayment(Appointment $appointment, float $amount): void
    {
        $paid = (float) $appointment->amount_paid + $amount;
        $price = (float) $appointment->price;
        $status = 'partial';

        if ($paid <= 0) {
            $status = 'unpaid';
        } elseif ($price > 0 && $paid >= $price) {
            $status = 'paid';
            $paid = $price;
        }

        $appointment->update([
            'amount_paid' => $paid,
            'payment_status' => $status,
        ]);
    }
}
