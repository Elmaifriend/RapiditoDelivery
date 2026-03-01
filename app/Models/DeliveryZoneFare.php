<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DeliveryZoneFare extends Model
{
    use HasFactory;

    protected $fillable = [
        'from_zone_id',
        'to_zone_id',
        'price',
        'active',
    ];

    protected $casts = [
        'price'  => 'decimal:2',
        'active' => 'boolean',
    ];

    public function fromZone()
    {
        return $this->belongsTo(DeliveryZone::class, 'from_zone_id');
    }

    public function toZone()
    {
        return $this->belongsTo(DeliveryZone::class, 'to_zone_id');
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}