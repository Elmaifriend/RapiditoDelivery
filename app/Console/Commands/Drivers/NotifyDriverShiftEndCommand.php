<?php

namespace App\Console\Commands\Drivers;

use App\Enums\DayOfWeek;
use App\Models\Driver;
use App\Models\Schedule;
use App\Services\WhatsAppNotifierService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class NotifyDriverShiftEndCommand extends Command
{
    protected $signature = 'drivers:notify-shift-end';
    protected $description = 'Notifica a los repartidores 5 minutos antes de la finalización de su turno.';

    public function handle(WhatsAppNotifierService $notifier): int
    {
        $now = Carbon::now();
        
        // Evaluamos la ventana de tiempo para el turno que finaliza en ~5 minutos
        $targetTime = $now->copy()->addMinutes(5);
        
        $currentDayEnum = DayOfWeek::from($targetTime->dayOfWeekIso);
        $todayDate = $targetTime->format('Y-m-d');

        // Rango de tolerancia de 1 minuto para prevenir pérdida por micro-retrasos del Cron
        $timeFrom = $targetTime->copy()->subSeconds(30)->format('H:i:s');
        $timeTo = $targetTime->copy()->addSeconds(30)->format('H:i:s');

        $schedules = Schedule::query()
            ->where('scheduleable_type', Driver::class)
            ->where('is_active', true)
            ->where('day_of_week', $currentDayEnum)
            ->whereTime('end_time', '>=', $timeFrom)
            ->whereTime('end_time', '<=', $timeTo)
            ->whereHasMorph('scheduleable', [Driver::class], function ($query) {
                $query->where('is_active', true);
            })
            ->with(['scheduleable.user'])
            ->get();

        foreach ($schedules as $schedule) {
            /** @var Driver|null $driver */
            $driver = $schedule->scheduleable;

            if (!$driver || !$driver->is_active) {
                continue;
            }

            // Evitar duplicados usando Cache Lock de 12 horas
            $cacheKey = "driver_shift_end_notified_{$driver->id}_{$todayDate}_{$schedule->id}";
            $shouldNotify = Cache::add($cacheKey, true, now()->addHours(12));

            if ($shouldNotify) {
                $sent = $notifier->notifyDriverShiftEndReminder($driver, $schedule->end_time);
                
                if (!$sent) {
                    Cache::forget($cacheKey);
                }
            }
        }

        return self::SUCCESS;
    }
}