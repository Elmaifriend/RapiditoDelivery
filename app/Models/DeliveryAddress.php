<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\AddressSource;

class DeliveryAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'guest_token',
        'label',
        'formatted_address', 
        'is_default',
        'source',  //GPS, WEB, APP, WhatsApp
        'address_line', 
        'reference',
        'photo_path',
        'city',
        'state',
        'country',
        'lat',
        'lng',
        'place_id',
        'last_used_at',
    ];

    protected $casts = [
        'source' => AddressSource::class,
        'lat' => 'float',
        'lng' => 'float',
        'is_default' => 'boolean',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function coordinates(): array
    {
        return [
            'lat' => $this->lat,
            'lng' => $this->lng,
        ];
    }
}