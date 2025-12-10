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
        schema::table('company_branches', function (Blueprint $table) {
            $table->string('working_hours_from')->nullable()->after('working_hours');
            $table->string('working_hours_to')->nullable()->after('working_hours_from');

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
