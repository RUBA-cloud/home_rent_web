<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('home_rents', function (Blueprint $table) {
            if (!Schema::hasColumn('home_rents', 'is_feature')) {
                $table->boolean('is_feature')->default(false);
            }
        });
    }

    public function down(): void
    {
        Schema::table('home_rents', function (Blueprint $table) {
            if (Schema::hasColumn('home_rents', 'is_feature')) {
                $table->dropColumn('is_feature');
            }
        });
    }
};
