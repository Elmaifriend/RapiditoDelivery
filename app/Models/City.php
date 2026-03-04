<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Restaurant;

class City extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'state',
        'country',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function serviceZones()
    {
        return $this->hasMany(ServiceZone::class);
    }

    public function restaurants(){
        return $this->hasMany(Restaurant::class);
    }
}