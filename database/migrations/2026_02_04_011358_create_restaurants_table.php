<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('restaurants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('status')->default('active'); // active, inactive, suspended, onboarding
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();

            $table->string('country')->default('MX');
            $table->string('state')->nullable();
            $table->string('city')->nullable();
            $table->string('address')->nullable();
            $table->string('postal_code')->nullable();

            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->string('google_maps_url')->nullable();

            $table->string('logo_path')->nullable();
            $table->string('banner_path')->nullable();
            $table->string('reference_image')->nullable();

            // Operación básica
            $table->boolean('is_open')->default(true);
            $table->boolean('accepts_delivery')->default(true);
            $table->boolean('accepts_pickup')->default(true);

            $table->timestamps();

            $table->index(['lat', 'lng']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restaurants');
    }
};
