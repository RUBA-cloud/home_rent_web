<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('home_rent_home_feature', function (Blueprint $table) {
            $table->id();

            $table->foreignId('home_rent_id')
                ->constrained('home_rents')
                ->cascadeOnDelete();

            $table->foreignId('home_feature_id')
                ->constrained('home_features')
                ->cascadeOnDelete();

            $table->timestamps();

            $table->unique(['home_rent_id', 'home_feature_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('home_rent_home_feature');
    }
};
