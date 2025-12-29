<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// 1. Daily reminder for professionals (Yellow/Red status)
Schedule::command('remind:professional-registration')
    ->dailyAt('10:00')
    ->timezone('America/Argentina/Buenos_Aires');

// 2. Activity check for Analyst (Red alerts)
Schedule::command('check:professional-activity')
    ->dailyAt('11:00')
    ->timezone('America/Argentina/Buenos_Aires');

// 3. Weekly reminder for companies to approve hours (Per diagram: SEMANAL)
Schedule::command('notify:pending-hours --days=0')
    ->mondays()
    ->at('09:00')
    ->timezone('America/Argentina/Buenos_Aires');

// 4. Weekly reports to companies (Every Monday at 8:00 AM)
Schedule::command('reports:send-weekly')
    ->weeklyOn(1, '08:00')
    ->timezone('America/Argentina/Buenos_Aires');

// 5. Monthly reports to companies (1st of each month at 9:00 AM)
Schedule::command('reports:send-monthly')
    ->monthlyOn(1, '09:00')
    ->timezone('America/Argentina/Buenos_Aires');
