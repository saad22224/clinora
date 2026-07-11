<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use App\Models\SystemSetting;
use App\Services\WhatsAppService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendAppointmentReminders extends Command
{
    protected $signature = 'appointments:remind';

    protected $description = 'Send automatic WhatsApp appointment reminders to patients one day before their appointment';

    public function handle(WhatsAppService $whatsApp): int
    {
        if (! SystemSetting::isWhatsAppEnabled()) {
            $this->warn('WhatsApp service is globally disabled in platform settings. Skipping appointment reminders.');

            return self::SUCCESS;
        }

        $this->info('Searching for appointments scheduled for tomorrow...');

        $tomorrowStart = Carbon::tomorrow()->startOfDay();
        $tomorrowEnd = Carbon::tomorrow()->endOfDay();

        $remindingDate = now()->subdays(1);
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

        $this->info("Successfully sent {$count} WhatsApp reminder(s).");

        return self::SUCCESS;
    }
}
