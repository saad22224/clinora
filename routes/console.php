<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Expire Subscriptions automatically every day
Schedule::command('subscriptions:check')->daily();

// Send WhatsApp reminders to patients 1 day before appointment
Schedule::command('appointments:remind')->hourly();
