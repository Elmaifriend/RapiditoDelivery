<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\OrderLifecycleStatus;
use App\Enums\RestaurantDecisionStatus;
use App\Enums\DeliveryStatus;
use App\Enums\PaymentStatus;
use App\Enums\PaymentMethod;
use Faker\Provider\Payment;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {

            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('restaurant_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('driver_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // ORDER WORKFLOWS

            $table->string('lifecycle_status')
                ->default(OrderLifecycleStatus::DRAFT->value);

            $table->string('restaurant_decision_status')
                ->default(RestaurantDecisionStatus::PENDING->value);

            $table->string('delivery_status')
                ->nullable();

            $table->string('payment_status')
                ->default(PaymentStatus::PENDING->value);

            // MONEY

            $table->decimal('subtotal', 10, 2);
            $table->decimal('delivery_fee', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->string('payment_method')->default( PaymentMethod::CASH->value )->nullable();

            $table->timestamps();

            // INDEXES IMPORTANTES

            $table->index('lifecycle_status');
            $table->index('restaurant_decision_status');
            $table->index('delivery_status');
            $table->index('payment_status');

            $table->index('driver_id');
            $table->index('restaurant_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};