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
        Schema::create('offers_type', function (Blueprint $table) {
            $table->id();
            $table->string('name_en', 255)->nullable();
            $table->string('name_ar', 255)->nullable();
            $table->text('description_en')->nullable();
            $table->text('description_ar')->nullable();
            $table->boolean('is_discount')->nullable()->default(false);
            $table->decimal('is_product_count_gift', 10, 2)->default(0);
            $table->boolean('is_total_gift')->default(false);
            $table->decimal('discount_value_product', 10, 2)->nullable();
            $table->decimal('discount_value_delivery', 10, 2)->nullable();
            $table->decimal('products_count_to_get_gift_offer', 10, 2)->nullable();
            $table->decimal('product_count_gift', 10, 2)->default(0);
            $table->decimal('total_gift', 10, 2)->default(0);
                $table->boolean('is_active')->default(true);
            
                        $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offers_type');
    }
};
