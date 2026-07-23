<?php

namespace App\Models;

use App\Enums\AddressSource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDropoffLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'formatted_address',
        'address_line',
        'reference',
        'delivery_instructions',
        'source',
        'city',
        'state',
        'country',
        'lat',
        'lng',
        'place_id',
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
