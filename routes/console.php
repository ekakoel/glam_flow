<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('bookings:send-tomorrow-reminders')
    ->dailyAt('18:00')
    ->withoutOverlapping();

Schedule::command('payments:auto-settle-past-service')
    ->everyFifteenMinutes()
    ->withoutOverlapping();
