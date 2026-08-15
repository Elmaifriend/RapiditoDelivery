<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

//Bussines
Schedule::command('businesses:open')->everyMinute()->withoutOverlapping();
Schedule::command('businesses:close')->everyMinute()->withoutOverlapping();

//Drivers
Schedule::command('drivers:notify-shift-start')->everyMinute()->withoutOverlapping();
Schedule::command('drivers:notify-shift-end')->everyMinute()->withoutOverlapping();