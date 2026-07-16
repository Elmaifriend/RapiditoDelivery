<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            
            // Relaciones (Llaves foráneas)
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('business_id')->constrained();
            $table->foreignId('driver_id')->nullable()->constrained()->onDelete('set null');
            
            // Identificación y contacto del invitado (Ahora seguros si es un usuario registrado)
            $table->string('guest_token')->nullable()->index();
            $table->string('customer_name')->nullable();
            $table->string('customer_phone')->nullable();

            // Estados (delivery_status ahora acepta null)
            $table->string('lifecycle_status');
            $table->string('business_decision_status');
            $table->string('delivery_status')->nullable(); // <-- Corrección del error crítico
            $table->string('payment_status');
            
            // Detalles y montos
            $table->text('special_instructions')->nullable();
            $table->decimal('subtotal', 10, 2);
            $table->decimal('delivery_fee', 10, 2);
            $table->decimal('total', 10, 2);
            $table->string('payment_method');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};