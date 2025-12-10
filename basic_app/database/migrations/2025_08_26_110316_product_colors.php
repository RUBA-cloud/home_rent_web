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
       Schema::create('product_colors', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('product_id');   // better name: match target table!
    $table->string('colors'); // ideally: text if storing JSON array
    $table->timestamps();

    $table->foreign('product_id')
          ->references('id')
          ->on('products')
          ->onDelete('cascade');
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
