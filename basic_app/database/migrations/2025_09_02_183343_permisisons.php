<?php // database/migrations/2025_09_02_000000_create_permissions_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('module_id'); // FK → modules_history
            $table->unsignedBigInteger('user_id'); // FK → modules_history
            $table->string('name_en');
            $table->string('module_name');
            $table->string('name_ar');
            $table->boolean('can_edit')->default(false);
            $table->boolean('can_delete')->default(false);
            $table->boolean('can_add')->default(false);
            $table->boolean('can_view_history')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // adjust table name if your PK is different
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

                 $table->foreign('module_id')
                ->references('id')
                ->on('modules')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
