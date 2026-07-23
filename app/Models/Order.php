<?php

namespace App\Models;

use App\Enums\DeliveryStatus;
use App\Enums\OrderLifecycleStatus;
use App\Enums\PaymentStatus;
use App\Enums\BusinessDecisionStatus;
use App\Enums\PaymentMethod;
use App\Enums\DeliveryOutcome;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'business_id',
        'driver_id',
        'guest_token',
        'customer_name',
        'customer_phone',
        'lifecycle_status',
        'business_decision_status',
        'delivery_status',
        'payment_status',
        'delivery_outcome',
        'delivery_notes',
        'special_instructions',
        'subtotal',
        'delivery_fee',
        'total',
        'payment_method',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'total' => 'decimal:2',
        'lifecycle_status' => OrderLifecycleStatus::class,
        'business_decision_status' => BusinessDecisionStatus::class,
        'delivery_status' => DeliveryStatus::class,
        'payment_status' => PaymentStatus::class,
        "payment_method" => PaymentMethod::class,
        'delivery_outcome' => DeliveryOutcome::class,
    ];

    public function dropoffLocations()
    {
        return $this->hasMany(OrderDropoffLocation::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function recalculateTotals(): void
    {
        $subtotal = $this->items()->sum('subtotal');

        $this->subtotal = $subtotal;
        $this->total = $subtotal + $this->delivery_fee;

        $this->save();
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }
}
