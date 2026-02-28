<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
}