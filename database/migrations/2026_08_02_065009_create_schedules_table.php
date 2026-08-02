<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();

            // Genera scheduleable_id (unsignedBigInteger) y scheduleable_type (string)
            $table->morphs('scheduleable');

            // Día de la semana (1 = Lunes, 7 = Domingo - ISO)
            $table->unsignedTinyInteger('day_of_week');

            // Horas de inicio y fin
            $table->time('start_time');
            $table->time('end_time');

            // Estado de actividad del horario
            $table->boolean('is_active')->default(true);

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};