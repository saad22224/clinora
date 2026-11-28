<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Scopes\TenantScope;
use Carbon\Carbon;

class AppointmentTrackingService
{
    public function __construct(
        private AppointmentQueueService $queue,
    ) {}

    public function findByToken(string $token): ?Appointment
    {
        if ($token === '' || strlen($token) < 32) {
            return null;
        }

        return Appointment::withoutGlobalScope(TenantScope::class)
            ->with(['clinic', 'doctor', 'visitType', 'patient'])
            ->where('tracking_token', $token)
            ->first();
    }

    /**
     * Minimal public payload — never exposes phone, notes, or other patients.
     *
     * @return array<string, mixed>
     */
    public function publicPayload(Appointment $appointment): array
    {
        $trackable = $this->isTrackable($appointment);
        $positionAhead = $trackable ? $this->queue->positionAhead($appointment) : null;

        return [
            'token' => $appointment->tracking_token,
            'clinic_name' => $appointment->clinic?->name,
            'doctor_name' => $appointment->doctor?->name,
            'visit_type' => $appointment->visitType?->name,
            'appointment_date' => $appointment->queue_date
                ? Carbon::parse($appointment->queue_date)->toDateString()
                : optional($appointment->scheduled_at)?->toDateString(),
            'appointment_date_label' => $appointment->scheduled_at
                ? $appointment->scheduled_at->timezone($this->queue->timezone())->translatedFormat('l، d F Y').' — '.$appointment->scheduled_at->timezone($this->queue->timezone())->format('h:i A')
                : null,
            'queue_number' => $appointment->queue_number,
            'queue_status' => $appointment->queue_status,
            'queue_status_label' => $this->queueStatusLabel($appointment->queue_status),
            'booking_status' => $appointment->status,
            'booking_status_label' => $this->bookingStatusLabel($appointment->status),
            'position_ahead' => $positionAhead,
            'estimated_position' => $positionAhead === null ? null : $positionAhead + 1,
            'is_your_turn' => $appointment->queue_status === AppointmentQueueService::STATUS_CALLED
                || $appointment->queue_status === AppointmentQueueService::STATUS_IN_PROGRESS,
            'is_trackable' => $trackable,
            'message' => $this->statusMessage($appointment, $positionAhead),
        ];
    }

    public function isTrackable(Appointment $appointment): bool
    {
        if (in_array($appointment->status, ['cancelled', 'rejected'], true)) {
            return false;
        }

        if (! $appointment->queue_date) {
            return false;
        }

        $day = Carbon::parse($appointment->queue_date, $this->queue->timezone())->startOfDay();
        $cutoff = $this->queue->today()->copy()->subDays(7);

        return $day->gte($cutoff);
    }

    private function queueStatusLabel(?string $status): string
    {
        return match ($status) {
            AppointmentQueueService::STATUS_WAITING => 'في الانتظار',
            AppointmentQueueService::STATUS_CALLED => 'تمت المناداة',
            AppointmentQueueService::STATUS_IN_PROGRESS => 'قيد الكشف',
            AppointmentQueueService::STATUS_COMPLETED => 'مكتمل',
            AppointmentQueueService::STATUS_SKIPPED => 'تم التخطي',
            default => 'غير محدد',
        };
    }

    private function bookingStatusLabel(?string $status): string
    {
        return match ($status) {
            'pending' => 'بانتظار التأكيد',
            'confirmed' => 'مؤكد',
            'completed' => 'مكتمل',
            'cancelled' => 'ملغي',
            'rejected' => 'مرفوض',
            default => (string) $status,
        };
    }

    private function statusMessage(Appointment $appointment, ?int $positionAhead): string
    {
        if (in_array($appointment->status, ['cancelled', 'rejected'], true)) {
            return 'هذا الموعد لم يعد قابلاً للتتبع.';
        }

        if ($appointment->queue_status === AppointmentQueueService::STATUS_COMPLETED) {
            return 'تم إكمال موعدك. شكرًا لزيارتك.';
        }

        if ($appointment->queue_status === AppointmentQueueService::STATUS_SKIPPED) {
            return 'تم تخطي دورك مؤقتًا. يرجى مراجعة الاستقبال.';
        }

        if ($appointment->queue_status === AppointmentQueueService::STATUS_CALLED) {
            return 'حان دورك الآن. تفضّل إلى غرفة الكشف.';
        }

        if ($appointment->queue_status === AppointmentQueueService::STATUS_IN_PROGRESS) {
            return 'أنت قيد الكشف حاليًا.';
        }

        if ($positionAhead === 0) {
            return 'أنت التالي في الدور.';
        }

        if ($positionAhead !== null && $positionAhead > 0) {
            return "يوجد {$positionAhead} مريض أمامك في الدور.";
        }

        return 'تم تسجيل حجزك. تابع هذه الصفحة لمعرفة دورك.';
    }
}
