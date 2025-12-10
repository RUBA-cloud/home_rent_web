<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('offers_history', function (Blueprint $table) {
            $table->id();

            // Foreign key to offer types
            $table->unsignedBigInteger('type_id');
            // $table->foreign('type_id')->references('id')->on('offers_type')->onDelete('cascade');

            // JSON array for multiple category IDs
            $table->json('category_ids');

            // Offer details
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->boolean('is_active')->default(true);

            // Foreign key to user who created the offer
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offers_history');
    }
};
