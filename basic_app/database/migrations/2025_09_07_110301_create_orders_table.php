<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')
                ->constrained()                 // references orders.id
                ->cascadeOnDelete();

            $table->foreignId('product_id')
                ->constrained()                 // references products.id
                ->restrictOnDelete();

            $table->string('color', 50)->nullable();
            $table->unsignedInteger('quantity');
            $table->decimal('price', 10, 2);        // unit price
            $table->decimal('total_price', 12, 2);  // quantity * price (stored)

            $table->timestamps();

            $table->index(['order_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
