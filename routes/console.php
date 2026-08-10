<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


Schedule::command('drivers:notify-shifts')->everyMinute();
Schedule::command('businesses:open')->everyMinute()->withoutOverlapping();
Schedule::command('businesses:close')->everyMinute()->withoutOverlapping();