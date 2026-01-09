<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_table', function (Blueprint $table) {
            $table->id();

            // ✅ Foreign keys
            $table->foreignId('user_id')
                ->constrained() // defaults to users.id
                ->cascadeOnDelete();

            $table->foreignId('home_id')
                ->constrained('home_rents') // home_rents.id
                ->cascadeOnDelete();

            // ✅ Dates
            $table->dateTime('from_date');
            $table->dateTime('end_date');

            // ✅ Counts
            $table->unsignedSmallInteger('adults_count')->default(1);
            $table->unsignedSmallInteger('children_count')->default(0);

            $table->timestamps();

            // (اختياري) فهارس لتحسين الاستعلامات
            $table->index(['home_id', 'from_date', 'end_date']);
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_table');
    }
};
