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
        Schema::create('company_branches', function (Blueprint $table) {
            $table->id();
            $table->string('name_en', 25);
            $table->string('name_ar', 25);
            $table->boolean('is_active')->default(false);
            $table->string('phone', 15)->nullable();
            $table->string('email')->nullable();
            $table->string('address_en');
            $table->string('address_ar');
            $table->string('location', 500)->nullable(); // set max length to 500
            $table->string('image')->nullable();
            $table->string('working_hours')->nullable();
            $table->string('working_days')->nullable();
            $table->string('fax')->nullable();
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
        Schema::dropIfExists('company_branches');
    }
};
