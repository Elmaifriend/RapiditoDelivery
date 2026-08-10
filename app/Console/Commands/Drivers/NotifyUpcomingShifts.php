<?php

namespace App\Console\Commands\Drivers;

use App\Enums\DayOfWeek;
use App\Models\Schedule;
use App\Services\WhatsAppNotifierService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class NotifyUpcomingShifts extends Command
{
    protected $signature = 'drivers:notify-shifts';

    protected $description = 'Revisa los horarios de los repartidores y envía un recordatorio por WhatsApp 10 minutos antes de su hora de entrada.';

    public function handle(WhatsAppNotifierService $notifier): int
    {
        $now = Carbon::now();
        
        // Obtenemos el día actual formateado como tu Enum DayOfWeek (1 = Lunes, 7 = Domingo)
        $currentDayEnum = DayOfWeek::from($now->dayOfWeekIso);

        // Calculamos el objetivo exacto: la hora actual + 10 minutos (HH:MM:00)
        $targetTime = $now->copy()->addMinutes(10)->format('H:i:00');

        // Buscamos agendas activas de tipo Driver que coincidan con el día y hora de inicio
        $schedules = Schedule::where('scheduleable_type', \App\Models\Driver::class)
            ->where('is_active', true)
            ->where('day_of_week', $currentDayEnum)
            ->whereTime('start_time', $targetTime)
            ->with(['scheduleable.user'])
            ->get();

        if ($schedules->isEmpty()) {
            return self::SUCCESS;
        }

        $this->info("Se encontraron {$schedules->count()} repartidores con turno a las {$targetTime}.");

        foreach ($schedules as $schedule) {
            /** @var \App\Models\Driver|null $driver */
            $driver = $schedule->scheduleable;

            if (!$driver) {
                continue;
            }

            $formattedStartTime = Carbon::parse($schedule->start_time)->format('g:i A');
            $sent = $notifier->notifyDriverShiftReminder($driver, $formattedStartTime);

            if ($sent) {
                $this->info("Notificación enviada con éxito al driver ID: {$driver->id}");
            } else {
                $this->error("No se pudo enviar la notificación al driver ID: {$driver->id}");
            }
        }

        return self::SUCCESS;
    }
}