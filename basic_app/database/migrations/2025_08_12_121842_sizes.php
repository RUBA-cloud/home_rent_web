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
        //
        Schema::create('sizes', function (Blueprint $table) {
      $table->id();
    $table->string('name_en', 25);
    $table->string('name_ar', 25);
    $table->boolean('is_active')->default(true);

    // Use a unique index name specific to the sizes table to avoid conflicts
    $table->unique(['name_en', 'name_ar'], 'unique_sizes_name_en_name_ar');

    $table->unsignedBigInteger('user_id')->nullable();
    $table->foreign('user_id')
          ->references('id')
          ->on('users')
          ->onDelete('set null');

    $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('sizes');
    }
};
