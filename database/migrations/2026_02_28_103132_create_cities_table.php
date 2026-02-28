<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cities', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('state')->nullable();
            $table->string('country')->default('Mexico');
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->unique(['name', 'state', 'country']);
            $table->index('active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cities');
    }
};