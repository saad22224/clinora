<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\ClinicQueueControl;
use App\Models\Scopes\TenantScope;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use RuntimeException;

class AppointmentQueueService
{
    public const STATUS_WAITING = 'waiting';

    public const STATUS_CALLED = 'called';

    public const STATUS_IN_PROGRESS = 'in_progress';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_SKIPPED = 'skipped';

    /**
     * @var list<string>
     */
    public const ACTIVE_QUEUE_STATUSES = [
        self::STATUS_WAITING,
        self::STATUS_CALLED,
        self::STATUS_IN_PROGRESS,
    ];

    public function timezone(): string
    {
        return (string) config('clinora.clinic_timezone', 'Africa/Cairo');
    }

    public function today(): Carbon
    {
        return Carbon::now($this->timezone())->startOfDay();
    }

    public function queueDateFor(CarbonInterface|string $scheduledAt): Carbon
    {
        return Carbon::parse($scheduledAt, $this->timezone())->timezone($this->timezone())->startOfDay();
    }

    public function resolveDoctorId(Clinic $clinic, ?int $preferredDoctorId = null): int
    {
        if ($preferredDoctorId) {
            $exists = User::query()
                ->where('clinic_id', $clinic->id)
                ->whereKey($preferredDoctorId)
                ->exists();

            if ($exists) {
                return $preferredDoctorId;
            }
        }

        $doctorId = User::query()
            ->where('clinic_id', $clinic->id)
            ->where('role', 'doctor')
            ->value('id');

        if ($doctorId) {
            return (int) $doctorId;
        }

        $ownerId = User::query()
            ->where('clinic_id', $clinic->id)
            ->where('role', 'owner')
            ->value('id');

        if ($ownerId) {
            return (int) $ownerId;
        }

        $fallback = User::query()->where('clinic_id', $clinic->id)->value('id');

        if (! $fallback && $clinic->organization_id) {
            $fallback = $clinic->organization?->memberships()
                ->orderByRaw("case when role = 'owner' then 0 else 1 end")
                ->value('user_id');
        }

        if (! $fallback) {
            throw new RuntimeException('لا يوجد طبيب أو مستخدم مرتبط بهذه العيادة لتعيين الدور.');
        }

        return (int) $fallback;
    }

    /**
     * Assign queue number + waiting status + tracking token for a newly created appointment.
     */
    public function assignOnCreate(Appointment $appointment): Appointment
    {
        return DB::transaction(function () use ($appointment) {
            $locked = Appointment::withoutGlobalScope(TenantScope::class)
                ->whereKey($appointment->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($locked->queue_number !== null && $locked->tracking_token) {
                return $locked;
            }

            $queueDate = $this->queueDateFor($locked->scheduled_at);
            $doctorId = (int) $locked->doctor_id;

            $max = Appointment::withoutGlobalScope(TenantScope::class)
                ->where('clinic_id', $locked->clinic_id)
                ->where('doctor_id', $doctorId)
                ->whereDate('queue_date', $queueDate->toDateString())
                ->lockForUpdate()
                ->max('queue_number');

            $locked->forceFill([
                'queue_number' => ((int) $max) + 1,
                'queue_date' => $queueDate->toDateString(),
                'queue_status' => self::STATUS_WAITING,
                'tracking_token' => $locked->tracking_token ?: bin2hex(random_bytes(32)),
            ])->save();

            return $locked->fresh();
        });
    }

    /**
     * Reassign queue number when the appointment moves to a different calendar day.
     */
    public function reassignForReschedule(Appointment $appointment, CarbonInterface|string $newScheduledAt): Appointment
    {
        return DB::transaction(function () use ($appointment, $newScheduledAt) {
            $locked = Appointment::withoutGlobalScope(TenantScope::class)
                ->whereKey($appointment->id)
                ->lockForUpdate()
                ->firstOrFail();

            $queueDate = $this->queueDateFor($newScheduledAt);

            $max = Appointment::withoutGlobalScope(TenantScope::class)
                ->where('clinic_id', $locked->clinic_id)
                ->where('doctor_id', $locked->doctor_id)
                ->whereDate('queue_date', $queueDate->toDateString())
                ->where('id', '!=', $locked->id)
                ->lockForUpdate()
                ->max('queue_number');

            $parsedSchedule = Carbon::parse($newScheduledAt);
            // Preserve the selected clock time; only queue_date uses the calendar day.
            $locked->forceFill([
                'scheduled_at' => $parsedSchedule,
                'queue_number' => ((int) $max) + 1,
                'queue_date' => $queueDate->toDateString(),
                'queue_status' => self::STATUS_WAITING,
                'called_at' => null,
                'in_progress_at' => null,
                'tracking_token' => $locked->tracking_token ?: bin2hex(random_bytes(32)),
            ])->save();

            return $locked->fresh();
        });
    }

    public function callNext(Clinic $clinic, int $doctorId, ?CarbonInterface $date = null): ?Appointment
    {
        $queueDate = ($date ? $this->queueDateFor($date) : $this->today())->toDateString();

        if ($this->isPaused($clinic->id, $doctorId, $queueDate)) {
            throw new RuntimeException('المناداة متوقفة مؤقتًا لهذا اليوم.');
        }

        return DB::transaction(function () use ($clinic, $doctorId, $queueDate) {
            $next = $this->eligibleWaitingQuery($clinic->id, $doctorId, $queueDate)
                ->lockForUpdate()
                ->orderBy('queue_number')
                ->first();

            if (! $next) {
                return null;
            }

            return $this->transition($next, self::STATUS_CALLED);
        });
    }

    public function call(Appointment $appointment): Appointment
    {
        return $this->transition($appointment, self::STATUS_CALLED);
    }

    public function markInProgress(Appointment $appointment): Appointment
    {
        return $this->transition($appointment, self::STATUS_IN_PROGRESS);
    }

    public function complete(Appointment $appointment): Appointment
    {
        return DB::transaction(function () use ($appointment) {
            $updated = $this->transition($appointment, self::STATUS_COMPLETED);
            $updated->forceFill(['status' => 'completed'])->save();

            return $updated->fresh();
        });
    }

    public function skip(Appointment $appointment): Appointment
    {
        if ($appointment->queue_status === self::STATUS_SKIPPED) {
            return $appointment->fresh(['patient', 'visitType', 'doctor']) ?? $appointment;
        }

        return $this->transition($appointment, self::STATUS_SKIPPED);
    }

    public function setPaused(Clinic $clinic, int $doctorId, bool $paused, ?CarbonInterface $date = null): ClinicQueueControl
    {
        $queueDate = ($date ? $this->queueDateFor($date) : $this->today())->toDateString();

        return ClinicQueueControl::query()->updateOrCreate(
            [
                'clinic_id' => $clinic->id,
                'doctor_id' => $doctorId,
                'queue_date' => $queueDate,
            ],
            ['is_paused' => $paused],
        );
    }

    public function isPaused(int $clinicId, int $doctorId, string $queueDate): bool
    {
        return (bool) ClinicQueueControl::query()
            ->where('clinic_id', $clinicId)
            ->where('doctor_id', $doctorId)
            ->whereDate('queue_date', $queueDate)
            ->value('is_paused');
    }

    /**
     * Number of eligible waiting patients ahead of this appointment (same scope).
     */
    public function positionAhead(Appointment $appointment): ?int
    {
        if (! $appointment->queue_number || ! $appointment->queue_date) {
            return null;
        }

        if (in_array($appointment->queue_status, [self::STATUS_COMPLETED, self::STATUS_SKIPPED], true)) {
            return 0;
        }

        if (in_array($appointment->status, ['cancelled', 'rejected'], true)) {
            return null;
        }

        return (int) $this->eligibleWaitingQuery(
            (int) $appointment->clinic_id,
            (int) $appointment->doctor_id,
            Carbon::parse($appointment->queue_date)->toDateString(),
        )
            ->where('queue_number', '<', $appointment->queue_number)
            ->count();
    }

    /**
     * @return array{
     *     total: int,
     *     waiting: int,
     *     called: int,
     *     in_progress: int,
     *     completed: int,
     *     skipped: int,
     *     next: ?Appointment,
     *     is_paused: bool
     * }
     */
    public function daySummary(int $clinicId, int $doctorId, CarbonInterface|string $date): array
    {
        $queueDate = $this->queueDateFor($date)->toDateString();

        $base = Appointment::withoutGlobalScope(TenantScope::class)
            ->where('clinic_id', $clinicId)
            ->where('doctor_id', $doctorId)
            ->whereDate('queue_date', $queueDate)
            ->whereNotIn('status', ['cancelled', 'rejected']);

        return [
            'total' => (clone $base)->count(),
            'waiting' => (clone $base)->where('queue_status', self::STATUS_WAITING)->count(),
            'called' => (clone $base)->where('queue_status', self::STATUS_CALLED)->count(),
            'in_progress' => (clone $base)->where('queue_status', self::STATUS_IN_PROGRESS)->count(),
            'completed' => (clone $base)->where('queue_status', self::STATUS_COMPLETED)->count(),
            'skipped' => (clone $base)->where('queue_status', self::STATUS_SKIPPED)->count(),
            'next' => $this->eligibleWaitingQuery($clinicId, $doctorId, $queueDate)->orderBy('queue_number')->first(),
            'is_paused' => $this->isPaused($clinicId, $doctorId, $queueDate),
        ];
    }

    /**
     * @return Collection<int, Appointment>
     */
    public function dayQueue(int $clinicId, ?int $doctorId, CarbonInterface|string $date): Collection
    {
        $queueDate = $this->queueDateFor($date)->toDateString();

        return Appointment::withoutGlobalScope(TenantScope::class)
            ->with(['patient', 'visitType', 'doctor'])
            ->where('clinic_id', $clinicId)
            ->when($doctorId, fn ($q) => $q->where('doctor_id', $doctorId))
            ->whereDate('queue_date', $queueDate)
            ->whereNotIn('status', ['cancelled', 'rejected'])
            ->orderByRaw("case when queue_status = 'skipped' then 1 else 0 end")
            ->orderByRaw('queue_number is null')
            ->orderBy('queue_number')
            ->orderBy('scheduled_at')
            ->get();
    }

    public function transition(Appointment $appointment, string $toStatus): Appointment
    {
        return DB::transaction(function () use ($appointment, $toStatus) {
            $locked = Appointment::withoutGlobalScope(TenantScope::class)
                ->whereKey($appointment->id)
                ->lockForUpdate()
                ->firstOrFail();

            if (in_array($locked->status, ['cancelled', 'rejected'], true)) {
                throw new InvalidArgumentException('لا يمكن تحديث دور موعد ملغي أو مرفوض.');
            }

            $from = $locked->queue_status;
            $allowed = match ($toStatus) {
                self::STATUS_CALLED => [self::STATUS_WAITING, self::STATUS_CALLED, self::STATUS_SKIPPED, null],
                self::STATUS_IN_PROGRESS => [self::STATUS_WAITING, self::STATUS_CALLED, self::STATUS_IN_PROGRESS],
                self::STATUS_COMPLETED => [self::STATUS_WAITING, self::STATUS_CALLED, self::STATUS_IN_PROGRESS],
                self::STATUS_SKIPPED => [self::STATUS_WAITING, self::STATUS_CALLED, self::STATUS_IN_PROGRESS, null],
                self::STATUS_WAITING => [self::STATUS_SKIPPED, self::STATUS_CALLED],
                default => [],
            };

            if (! in_array($from, $allowed, true)) {
                throw new InvalidArgumentException('انتقال حالة الدور غير مسموح من ['.($from ?? 'null').'] إلى ['.$toStatus.'].');
            }

            $payload = ['queue_status' => $toStatus];

            if ($toStatus === self::STATUS_CALLED) {
                $payload['called_at'] = now();
            }

            if ($toStatus === self::STATUS_IN_PROGRESS) {
                $payload['in_progress_at'] = now();
                if (! $locked->called_at) {
                    $payload['called_at'] = now();
                }
            }

            $locked->forceFill($payload)->save();

            return $locked->fresh(['patient', 'visitType', 'doctor']);
        });
    }

    private function eligibleWaitingQuery(int $clinicId, int $doctorId, string $queueDate)
    {
        return Appointment::withoutGlobalScope(TenantScope::class)
            ->where('clinic_id', $clinicId)
            ->where('doctor_id', $doctorId)
            ->whereDate('queue_date', $queueDate)
            ->where('queue_status', self::STATUS_WAITING)
            ->whereNotIn('status', ['cancelled', 'rejected']);
    }
}
