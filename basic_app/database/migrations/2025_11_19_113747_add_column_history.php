<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('orders_history', function (Blueprint $table) {
            $table->id();

            // Customer placing the order
            $table->foreignId('user_id')
                ->constrained()                 // references users.id
                ->cascadeOnDelete();

            // Employee handling the order (optional). Adjust table if you have a separate employees table.
            $table->foreignId('employee_id')
                ->nullable()
                ->constrained('users')          // change to ->constrained('employees') if needed
                ->nullOnDelete();
                $table->decimal('total_price', 10, 2)->default(0);
            // Address-related fields
            $table->string('building_number')->nullable();
            $table->string('street_name')->nullable();

            // Coordinates (decimal is better than string)
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('long', 10, 7)->nullable();
            // 0 = pending (default). Add your own mapping as needed (1=processing, 2=completed, etc.)
            $table->tinyInteger('status')->default(0)->index();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
