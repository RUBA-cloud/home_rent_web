<?php
// database/migrations/2025_09_10_000000_create_notifications_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('notifications_custom', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // null => broadcast to all
            $table->string('title', 200);
            $table->text('body')->nullable();
            $table->string('type', 50)->nullable();          // e.g., order, system, message
            $table->string('icon', 80)->nullable();          // e.g., 'fas fa-bell'
            $table->string('link')->nullable();              // where to go on click
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'read_at', 'created_at']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('notifications_custom');
    }
};
