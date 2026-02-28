<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_dropoff_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->decimal('lat', 10, 7);
            $table->decimal('lng', 10, 7);
            $table->string('source'); 
            $table->boolean('confirmed')->default(false);
            $table->timestamps();
            $table->index('order_id');
            $table->index('confirmed');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_dropoff_locations');
    }
};