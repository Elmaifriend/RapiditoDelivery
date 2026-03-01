<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_zone_fares', function (Blueprint $table) {
            $table->id();

            $table->foreignId('from_zone_id')
                ->constrained('delivery_zones')
                ->cascadeOnDelete();

            $table->foreignId('to_zone_id')
                ->constrained('delivery_zones')
                ->cascadeOnDelete();

            $table->decimal('price', 8, 2);

            $table->boolean('active')->default(true);

            $table->timestamps();

            $table->unique(['from_zone_id', 'to_zone_id']);
            $table->index(['from_zone_id', 'to_zone_id', 'active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_zone_fares');
    }
};