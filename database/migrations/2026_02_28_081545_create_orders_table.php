<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('driver_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status')->default('pending');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('delivery_fee', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->string('payment_status')->default('pending');

            $table->timestamps();

            $table->index(['status']);
            $table->index(['payment_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};