<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('employee_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();

            // what happened
            $table->string('action'); // created|updated|deleted

            // snapshot of old data (before change)
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('avatar_path')->nullable();
            $table->json('permissions_snapshot')->nullable();
            $table->boolean('is_active')->nullable()->default(true);

            // optional change context
            $table->json('meta')->nullable(); // IP, reason, etc.

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_histories');
    }
};
