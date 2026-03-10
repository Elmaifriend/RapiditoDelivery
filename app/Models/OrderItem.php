<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',

        'product_name_snapshot',
        'product_description_snapshot',
        'product_image_url_snapshot',

        'price_snapshot',
        'quantity',
        'subtotal',
    ];

    protected $casts = [
        'price_snapshot' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'quantity' => 'integer',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function recalculateSubtotal(): void
    {
        $this->subtotal = $this->price_snapshot * $this->quantity;
        $this->save();
    }
}