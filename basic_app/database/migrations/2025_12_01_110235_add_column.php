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
        Schema::table('offers', function (Blueprint $table) {
            // add the column (change ->default(1) or ->nullable() as you like)
            $table->unsignedBigInteger('type_id')
                  ->default(1); // or ->nullable();

            // add the foreign key
            $table->foreign('type_id')
                  ->references('id')
                  ->on('type')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('offers', function (Blueprint $table) {
            // drop FK then column on rollback
            $table->dropForeign(['type_id']);
            $table->dropColumn('type_id');
        });
    }
};
