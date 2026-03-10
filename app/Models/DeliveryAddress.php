<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'guest_token',
        'label',
        'formatted_address',
        'street',
        'street_number',
        'neighborhood',
        'city',
        'state',
        'postal_code',
        'country',
        'lat',
        'lng',
        'place_id',
    ];

    protected $casts = [
        'lat' => 'float',
        'lng' => 'float',
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