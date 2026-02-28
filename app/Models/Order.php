<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'restaurant_id',
        'driver_id',
        'status',
        'subtotal',
        'delivery_fee',
        'total',
        'payment_status',
    ];

    protected $casts = [
        'subtotal'      => 'decimal:2',
        'delivery_fee'  => 'decimal:2',
        'total'         => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function recalculateTotals(): void
    {
        $subtotal = $this->items()->get()->sum(function ($item) {
            return $item->subtotal;
        });

        $this->subtotal = $subtotal;
        $this->total = $subtotal + $this->delivery_fee;

        $this->save();
    }
}