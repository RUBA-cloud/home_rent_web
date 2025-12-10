<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add id column only if it doesn't already exist
        if (!Schema::hasColumn('regions_history', 'id')) {
            Schema::table('regions_history', function (Blueprint $table) {
                // Adds BIGINT unsigned auto-increment PRIMARY KEY
                $table->id()->first(); // 'first()' works on MySQL; remove if not needed
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('regions_history', 'id')) {
            Schema::table('regions_history', function (Blueprint $table) {
                // If your DB complains, drop the primary first:
                // $table->dropPrimary();  // uncomment if needed
                $table->dropColumn('id');
            });
        }
    }
};
