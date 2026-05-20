<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_zones', function (Blueprint $table) {
            $table->id();

            $table->foreignId('service_zone_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('name');

            $table->json('polygon_json');

            $table->decimal('bbox_min_lat', 10, 7);
            $table->decimal('bbox_max_lat', 10, 7);
            $table->decimal('bbox_min_lng', 10, 7);
            $table->decimal('bbox_max_lng', 10, 7);

            $table->decimal('delivery_price', 8, 2);

            $table->boolean('active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_zones');
    }
};
