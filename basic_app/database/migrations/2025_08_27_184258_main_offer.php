<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('offers', function (Blueprint $table) {
            $table->id();

            // // Foreign key to offer types
            // $table->foreignId('type_id')
            //       ->constrained('offer_types') // make sure your table is named offer_types
            //       ->cascadeOnDelete();

            // JSON array for multiple category IDs (or use pivot table instead)
            $table->json('category_ids')->nullable();

            // Offer details
            $table->decimal('discount_percentage', 5, 2)->default(0.00);
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->boolean('is_active')->default(true);

            // Foreign key to user who created the offer
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offers');
    }
};
