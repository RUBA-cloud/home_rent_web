<?php
// database/migrations/xxxx_xx_xx_create_device_tokens_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('device_tokens', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->string('token')->unique(); // FCM registration token
            $t->string('platform')->nullable(); // ios|android|web (optional)
            $t->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('device_tokens'); }
};
