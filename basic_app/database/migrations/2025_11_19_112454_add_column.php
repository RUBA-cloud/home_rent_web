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
        Schema::table('orders', function (Blueprint $table) {
            // Total price as decimal
            $table->decimal('total_price', 10, 2)->default(0);

            // Address-related fields
            $table->string('building_number')->nullable();
            $table->string('street_name')->nullable();

            // Coordinates (decimal is better than string)
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('long', 10, 7)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'total_price',
                'building_number',
                'street_name',
                'lat',
                'long',
            ]);
        });
    }
};
