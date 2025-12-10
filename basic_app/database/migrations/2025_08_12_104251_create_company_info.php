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
         Schema::create('company_info', function (Blueprint $table) {
        $table->id();
        $table->string('image')->nullable();
       $table->longText('name_en')->nullable();
       $table->longText('name_ar')->nullable();
        $table->longText('about_us_en')->nullable();
        $table->longText('about_us_ar')->nullable();
        $table->longText('mission_en')->nullable();
        $table->longText('mission_ar')->nullable();
        $table->longText('vision_en')->nullable();
        $table->longText('vision_ar')->nullable();
        $table->string('phone', 15)->nullable();
        $table->string('email')->nullable();
        $table->string('address_en')->nullable();
        $table->string('address_ar')->nullable();
        $table->string('location')->nullable();
        $table->string('main_color')->nullable(); // Assuming this is a hex color code
        $table->string('sub_color')->nullable(); // Assuming this is a hex color code
        $table->string('text_color')->nullable(); // Assuming this is a text color code
        $table->longText('button_color')->nullable();
       $table->longText('icon_color')->nullable();
       $table->longText('text_filed_color')->nullable();
       $table->longText('hint_color')->nullable();
       $table->longText('button_text_color')->nullable();
       $table->longText('card_color')->nullable();
       $table->longText('label_color')->nullable();
       $table->unsignedBigInteger('user_id')->nullable();
       $table->string('location')->nullable()->maxLength(500)->change();
        $table->string('is_active')->nullable()->maxLength(500)->change();
        $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        // URL or map location
        $table->timestamps(); // This adds created_at and updated_at
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
                Schema::dropIfExists('company_info');

    }
};
