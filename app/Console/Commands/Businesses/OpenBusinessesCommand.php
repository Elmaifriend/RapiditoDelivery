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

class OpenBusinessesCommand extends Command
{
    protected $signature = 'businesses:open';
    protected $description = 'Abre los restaurantes según su horario y notifica por WhatsApp.';

    public function handle(WhatsAppNotifierService $notifier): int
    {
        $now = Carbon::now();
        $currentDayEnum = DayOfWeek::from($now->dayOfWeekIso);
        $currentTime = $now->format('H:i:s');
        $todayDate = $now->format('Y-m-d');

        // Buscar horarios activos del día donde la hora actual ya haya alcanzado o superado el start_time
        // pero aún no haya superado el end_time.
        $schedules = Schedule::query()
            ->where('scheduleable_type', Business::class)
            ->where('is_active', true)
            ->where('day_of_week', $currentDayEnum)
            ->whereTime('start_time', '<=', $currentTime)
            ->whereTime('end_time', '>', $currentTime)
            ->whereHasMorph('scheduleable', [Business::class], function ($query) {
                $query->where('status', BusinessStatus::ACTIVE)
                      ->where('is_open', false); // Solo traemos los que están cerrados
            })
            ->with(['scheduleable'])
            ->get();

        foreach ($schedules as $schedule) {
            /** @var Business $business */
            $business = $schedule->scheduleable;

            if (!$business) {
                continue;
            }

            // Cambiar estado a abierto
            $business->update(['is_open' => true]);

            // Evitar notificaciones duplicadas en el mismo día/horario
            $cacheKey = "business_open_notified_{$business->id}_{$todayDate}_{$schedule->id}";
            $shouldNotify = Cache::add($cacheKey, true, now()->addHours(12));

            if ($shouldNotify) {
                $sent = $notifier->notifyBusinessStatusChange($business, 'open');
                if (!$sent) {
                    Cache::forget($cacheKey);
                }
            }
        }

        return self::SUCCESS;
    }
}