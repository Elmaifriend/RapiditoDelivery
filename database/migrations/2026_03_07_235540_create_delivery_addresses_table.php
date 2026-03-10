<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_addresses', function (Blueprint $table) {
            $table->id();

            // Owner
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('guest_token')->nullable()->index();

            // Metadata
            $table->string('label')->nullable(); // Casa, Trabajo, etc
            $table->string('formatted_address');

            // Address parts
            $table->string('street')->nullable();
            $table->string('street_number')->nullable();
            $table->string('neighborhood')->nullable();
            $table->string('city');
            $table->string('state')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country');

            // Coordinates
            $table->decimal('lat', 10, 7);
            $table->decimal('lng', 10, 7);

            // Google Place
            $table->string('place_id')->nullable()->index();

            $table->boolean('is_default')->default(false);
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();

            // Useful indexes
            $table->index(['user_id']);
            $table->index(['lat', 'lng']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_addresses');
    }
};