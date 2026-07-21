<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\DriverStatus;

class Driver extends Model
{
    protected $fillable = [
        'user_id', 
        'city_id', 
        'status'
    ];

    protected $casts = [
        'status' => DriverStatus::class,
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
}