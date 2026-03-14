<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'guest_token',
        'restaurant_id',
        'subtotal',
        'delivery_fee',
        'total',
        'status',
        'expires_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'total' => 'decimal:2',
        'expires_at' => 'datetime',
        'converted_to_order' => 'boolean',
        'converted_to_order_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    public function recalculateTotals(): void
    {
        $subtotal = $this->items()->sum('subtotal');

        $this->update([
            'subtotal' => $subtotal,
            'total' => $subtotal + $this->delivery_fee,
        ]);
    }
}
