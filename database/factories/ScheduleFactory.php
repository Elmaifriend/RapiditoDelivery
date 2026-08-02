<?php

namespace Database\Factories;

use App\Enums\DayOfWeek;
use App\Models\Business;
use App\Models\Schedule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Schedule>
 */
class ScheduleFactory extends Factory
{
    protected $model = Schedule::class;

    public function definition(): array
    {
        return [
            // Por defecto crea un modelo polimórfico apuntando a Business
            'scheduleable_type' => Business::class,
            'scheduleable_id' => Business::factory(),
            'day_of_week' => fake()->randomElement(DayOfWeek::cases()),
            'start_time' => '08:00:00',
            'end_time' => '20:00:00',
            'is_active' => true,
        ];
    }

    /**
     * Estado para definir un horario de todo el día / turno completo
     */
    public function fullDay(): static
    {
        return $this->state(fn (array $attributes) => [
            'start_time' => '00:00:00',
            'end_time' => '23:59:59',
        ]);
    }
}