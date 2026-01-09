<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('faviorates', function (Blueprint $table) {
            // ✅ drop FK then column (Laravel will drop FK automatically sometimes, but this is safer)
            if (Schema::hasColumn('faviorates', 'product_id')) {
                $table->dropConstrainedForeignId('product_id');
            }

            // ✅ add home_id
            if (!Schema::hasColumn('faviorates', 'home_id')) {
                $table->foreignId('home_id')
                    ->after('id')
                    ->constrained('home_rents')   // <-- change if your table name differs
                    ->onDelete('cascade');
            }
        });
    }

    public function down(): void
    {
        Schema::table('faviorates', function (Blueprint $table) {
            if (Schema::hasColumn('faviorates', 'home_id')) {
                $table->dropConstrainedForeignId('home_id');
            }

            if (!Schema::hasColumn('faviorates', 'product_id')) {
                $table->foreignId('product_id')
                    ->after('id')
                    ->constrained('products')
                    ->onDelete('cascade');
            }
        });
    }
};
