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
        Schema::create('home_rents', function (Blueprint $table) {
            $table->id();

            $table->string('name_en');
            $table->string('name_ar');

            // 🔗 Category relation
            $table->foreignId('category_id')
                ->constrained('categories')
                ->cascadeOnDelete();

            // ⭐ Ratings
            $table->double('total_ratings')->default(0);
            $table->double('average_rating')->default(0);

            // 📍 Location (better to use decimal for lat/long)
            $table->decimal('longitude', 10, 7);
            $table->decimal('latitude', 10, 7);

            // 🛏️ Details
            $table->integer('number_of_bedrooms');
            $table->integer('number_of_bathrooms');
            $table->double('rent_price');

            // 📝 Descriptions
            $table->text('description_en');
            $table->text('description_ar');

            // 🧩 Features as JSON
            $table->json('features')->nullable();

            // ✅ Availability (this was wrong before)
            $table->boolean('is_available')->default(true);

            // 📷 Media
            $table->string('image')->nullable();
            $table->string('video')->nullable();

            // 👤 Owner/User relation
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('home_rents');
    }
};
