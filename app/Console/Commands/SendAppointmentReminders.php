<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use App\Services\WhatsAppService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendAppointmentReminders extends Command
{
    protected $signature = 'appointments:remind';

    protected $description = 'إرسال تنبيهات وتذكيرات الواتساب تلقائياً للمرضى قبل موعدهم بيوم واحد';

    public function handle(WhatsAppService $whatsApp): int
    {
        $this->info('البحث عن المواعيد المحجوزة ليوم غد...');

        $tomorrowStart = Carbon::tomorrow()->startOfDay();
        $tomorrowEnd = Carbon::tomorrow()->endOfDay();

        $appointments = Appointment::with(['patient', 'clinic', 'visitType'])
            ->whereBetween('scheduled_at', [$tomorrowStart, $tomorrowEnd])
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('reminder_sent', false)
            ->get();

        $count = 0;

        foreach ($appointments as $app) {
            $phone = $app->patient?->phone;
            if (blank($phone)) {
                continue;
            }

            $timeStr = Carbon::parse($app->scheduled_at)->format('h:i A');
            $clinicName = $app->clinic?->name ?? 'العيادة';
            $serviceName = $app->visitType?->name ?? 'كشف طبي';

            $message = "تذكير بموعدك الطبي غداً ⏰\n\nمرحباً عزيزي المريض، نود تذكيرك بموعدك في *{$clinicName}*:\n• الخدمة: {$serviceName}\n• الموعد: غداً الساعة *{$timeStr}*\n\nيرجى الحضور قبل الموعد بـ 15 دقيقة. مع تمنياتنا لكم بدوام الصحة.";

            if ($whatsApp->sendMessage($phone, $message)) {
                $app->update(['reminder_sent' => true]);
                $count++;
            }
        }

        $this->info("تم إرسال {$count} تنبيه عبر الواتساب بنجاح.");

        return self::SUCCESS;
    }
}
