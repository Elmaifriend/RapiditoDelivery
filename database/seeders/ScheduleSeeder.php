<?php

namespace Database\Seeders;

use App\Enums\DayOfWeek;
use App\Models\Business;
use App\Models\Schedule;
use Illuminate\Database\Seeder;

class ScheduleSeeder extends Seeder
{
    public function run(): void
    {
        $businesses = Business::all();

        foreach ($businesses as $business) {
            foreach (DayOfWeek::cases() as $day) {
                Schedule::firstOrCreate(
                    [
                        'scheduleable_type' => Business::class,
                        'scheduleable_id' => $business->id,
                        'day_of_week' => $day->value,
                    ],
                    [
                        'start_time' => '08:00:00',
                        'end_time' => '20:00:00',
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}