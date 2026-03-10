<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id();

            // Owner
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('guest_token')->nullable()->index();

            // Optional restaurant association
            $table->foreignId('restaurant_id')->nullable()->constrained()->nullOnDelete();

            // Totals
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('delivery_fee', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);

            //Traking
            $table->string('status')->default('active');
            $table->timestamp('expires_at')->nullable();

            $table->timestamps();

            // Indexes
            $table->index(['user_id']);
            $table->index(['restaurant_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};