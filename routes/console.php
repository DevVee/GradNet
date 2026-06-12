<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ── Scheduled jobs ────────────────────────────────────────────────────
// Requires the OS to run `php artisan schedule:run` every minute.
// Windows: add a Task Scheduler entry. Linux/Mac: add to crontab.

Schedule::command('alumni:weekly-digest')
    ->weeklyOn(1, '08:00')  // Mondays at 8:00 AM
    ->description('Send weekly alumni digest email');
