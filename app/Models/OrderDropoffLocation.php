<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderDropoffLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'lat',
        'lng',
        'source',
        'confirmed',
    ];

    protected $casts = [
        'lat' => 'float',
        'lng' => 'float',
        'confirmed' => 'boolean',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}