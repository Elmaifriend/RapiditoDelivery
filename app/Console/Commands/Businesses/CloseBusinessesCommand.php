<?php

namespace App\Console\Commands\Businesses;

use App\Enums\BusinessStatus;
use App\Enums\DayOfWeek;
use App\Models\Business;
use App\Models\Schedule;
use App\Services\WhatsAppNotifierService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class CloseBusinessesCommand extends Command
{
    protected $signature = 'businesses:close';
    protected $description = 'Cierra los restaurantes según su horario y notifica por WhatsApp.';

    public function handle(WhatsAppNotifierService $notifier): int
    {
        $now = Carbon::now();
        $currentDayEnum = DayOfWeek::from($now->dayOfWeekIso);
        $currentTime = $now->format('H:i:s');
        $todayDate = $now->format('Y-m-d');

        // Buscar negocios activos que están abiertos pero cuya hora actual alcanzó o superó el end_time
        $schedules = Schedule::query()
            ->where('scheduleable_type', Business::class)
            ->where('is_active', true)
            ->where('day_of_week', $currentDayEnum)
            ->whereTime('end_time', '<=', $currentTime)
            ->whereHasMorph('scheduleable', [Business::class], function ($query) {
                $query->where('status', BusinessStatus::ACTIVE)
                      ->where('is_open', true); // Solo los que están actualmente abiertos
            })
            ->with(['scheduleable'])
            ->get();

        foreach ($schedules as $schedule) {
            /** @var Business $business */
            $business = $schedule->scheduleable;

            if (!$business) {
                continue;
            }

            // Cambiar estado a cerrado
            $business->update(['is_open' => false]);

            // Evitar notificaciones duplicadas en el mismo día/horario
            $cacheKey = "business_close_notified_{$business->id}_{$todayDate}_{$schedule->id}";
            $shouldNotify = Cache::add($cacheKey, true, now()->addHours(12));

            if ($shouldNotify) {
                $sent = $notifier->notifyBusinessStatusChange($business, 'close');
                if (!$sent) {
                    Cache::forget($cacheKey);
                }
            }
        }

        return self::SUCCESS;
    }
}