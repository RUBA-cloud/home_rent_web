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
        Schema::create('category_branch', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id')->nullable();
        $table->foreign('category_id')
              ->references('id')
              ->on('categories')
              ->onDelete('set null');
              $table->unsignedBigInteger('branch_id')->nullable();
        $table->foreign('branch_id')
              ->references('id')
              ->on('company_branches')
              ->onDelete('set null'); // Or u
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_branch');
    }
};
