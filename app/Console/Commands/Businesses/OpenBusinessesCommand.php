<?php 
namespace App\Console\Commands\Businesses;

use App\Enums\DayOfWeek;
use App\Models\Business;
use App\Models\Schedule;
use App\Services\WhatsAppNotifierService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class OpenBusinessesCommand extends Command
{
    protected $signature = 'businesses:open';
    protected $description = 'Abre los restaurantes según su horario y notifica por WhatsApp.';

    public function handle(WhatsAppNotifierService $notifier): int
    {
        $now = Carbon::now();
        $currentDayEnum = DayOfWeek::from($now->dayOfWeekIso);
        $currentTime = $now->format('H:i:00');
        $todayDate = $now->format('Y-m-d');

        // Buscar horarios de Business que coincidan con el día y la hora exacta de apertura
        $schedules = Schedule::where('scheduleable_type', Business::class)
            ->where('is_active', true)
            ->where('day_of_week', $currentDayEnum)
            ->whereTime('start_time', $currentTime)
            ->with(['scheduleable'])
            ->get();

        foreach ($schedules as $schedule) {
            /** @var Business|null $business */
            $business = $schedule->scheduleable;

            if (!$business || $business->status !== 'active') {
                continue;
            }

            // Cambiar estado a abierto
            $business->update(['is_open' => true]);

            // Prevenir doble notificación
            $cacheKey = "business_open_notified_{$business->id}_{$todayDate}_{$schedule->start_time}";
            $shouldNotify = Cache::add($cacheKey, true, now()->addHours(2));

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