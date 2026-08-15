<?php

namespace App\Console\Commands\Drivers;

use App\Enums\DayOfWeek;
use App\Models\Driver;
use App\Models\Schedule;
use App\Services\WhatsAppNotifierService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class NotifyDriverShiftStartCommand extends Command
{
    protected $signature = 'drivers:notify-shift-start';
    protected $description = 'Notifica a los repartidores 10 minutos antes del inicio de su turno.';

    public function handle(WhatsAppNotifierService $notifier): int
    {
        $now = Carbon::now();
        
        // Evaluamos la ventana de tiempo para el turno que inicia en ~10 minutos
        $targetTime = $now->copy()->addMinutes(10);
        
        $currentDayEnum = DayOfWeek::from($targetTime->dayOfWeekIso);
        $todayDate = $targetTime->format('Y-m-d');

        // Rango de tolerancia de 1 minuto para prevenir pérdida por micro-retrasos del Cron
        $timeFrom = $targetTime->copy()->subSeconds(30)->format('H:i:s');
        $timeTo = $targetTime->copy()->addSeconds(30)->format('H:i:s');

        // Buscar horarios de repartidores
        $schedules = Schedule::query()
            ->where('scheduleable_type', Driver::class)
            ->where('is_active', true)
            ->where('day_of_week', $currentDayEnum)
            ->whereTime('start_time', '>=', $timeFrom)
            ->whereTime('start_time', '<=', $timeTo)
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
            $cacheKey = "driver_shift_start_notified_{$driver->id}_{$todayDate}_{$schedule->id}";
            $shouldNotify = Cache::add($cacheKey, true, now()->addHours(12));

            if ($shouldNotify) {
                $sent = $notifier->notifyDriverShiftStartReminder($driver, $schedule->start_time);
                
                if (!$sent) {
                    Cache::forget($cacheKey);
                }
            }
        }

        return self::SUCCESS;
    }
}