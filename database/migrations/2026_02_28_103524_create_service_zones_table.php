<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_zones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->json('polygon');
            $table->decimal('min_lat', 10, 7);
            $table->decimal('max_lat', 10, 7);
            $table->decimal('min_lng', 10, 7);
            $table->decimal('max_lng', 10, 7);
            $table->boolean('active')->default(true);
            $table->boolean('debug')->default(false);
            $table->timestamps();

            $table->index('city_id');
            $table->index(['active', 'debug']);
            $table->index(['min_lat', 'max_lat']);
            $table->index(['min_lng', 'max_lng']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_zones');
    }
};