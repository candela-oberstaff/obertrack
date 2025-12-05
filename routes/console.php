<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Send pending hours notifications every Monday at 9 AM
Schedule::command('notify:pending-hours --days=7')
    ->weeklyOn(1, '9:00')
    ->timezone('America/Argentina/Buenos_Aires');
