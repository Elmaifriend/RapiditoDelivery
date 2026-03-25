<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Enums\AddressSource;

class OrderDropoffLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'formatted_address',
        'lat',
        'lng',
        'source',
    ];

    protected $casts = [
        'source' => AddressSource::class,
        'lat' => 'float',
        'lng' => 'float',
        'confirmed' => 'boolean',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}