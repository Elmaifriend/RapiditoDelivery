<?php

namespace App\Models;

use App\Enums\DriverAvailability;
use App\Enums\DriverOperationalStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Driver extends Model
{
    protected $fillable = [
        'user_id', 
        'city_id', 
        'availability_status',
        'operational_status',
    ];

    protected $casts = [
        'availability_status' => DriverAvailability::class,
        'operational_status'  => DriverOperationalStatus::class,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function schedules()
    {
        return $this->morphMany(\App\Models\Schedule::class, 'scheduleable');
    }
}