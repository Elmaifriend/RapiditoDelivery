<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();

            // Relationships
            $table->foreignId('cart_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete(); //Que hace esto?

            // Snapshot data
            $table->string('product_name_snapshot');
            $table->text('product_description_snapshot')->nullable();
            $table->string('product_image_url_snapshot')->nullable();
            $table->decimal('price_snapshot', 10, 2);

            // Purchase info
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('subtotal', 10, 2);

            $table->timestamps();

            // Indexes
            $table->index(['cart_id']);
            $table->index(['product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};