<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Auto-create leave quotas for new year (run on Jan 1 every year)
Schedule::command('app:create-annual-quotas')->yearlyOn(1, 1, '00:00');
