<?php

namespace App\Models;

use App\Enums\DayOfWeek;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'scheduleable_id',
        'scheduleable_type',
        'day_of_week',
        'start_time',
        'end_time',
        'is_active',
    ];

    protected $casts = [
        'day_of_week' => DayOfWeek::class,
        'is_active' => 'boolean',
    ];

    /**
     * Relación polimórfica padre (Business, User/Rider, etc.)
     */
    public function scheduleable(): MorphTo
    {
        return $this->morphTo();
    }
}