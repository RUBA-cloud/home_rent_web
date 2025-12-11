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
        Schema::create('home_feature_histories', function (Blueprint $table) {
            $table->id();

            $table->string('name_en');
            $table->string('name_ar');
 $table->text('description_en')->nullable();
            $table->text('description_ar')->nullable();
            $table->string('image')->nullable();
            // 👤 User relation
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // ✅ Active flag (fixed: boolval ❌ → boolean ✅)
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('home_feature_histories');}
};
