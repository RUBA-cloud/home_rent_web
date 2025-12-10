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

  Schema::table('offers_history', function (Blueprint $table) {
            //
        $table->string('start_date');

        $table->string('end_date');});
  }



    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
