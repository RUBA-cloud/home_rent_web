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
        Schema::create('modules_history', function (Blueprint $table) {
            $table->id();

            // ✅ Modules flags
            $table->boolean('company_dashboard_module')->default(false);
            $table->boolean('company_info_module')->default(false);
            $table->boolean('company_branch_module')->default(false);
            $table->boolean('company_category_module')->default(false);
            $table->boolean('company_type_module')->default(false);
            $table->boolean('company_size_module')->default(false);
            $table->boolean('company_offers_type_module')->default(false);
            $table->boolean('company_offers_module')->default(false);

            $table->boolean('home_rent_feature_module')->default(false);

            // ❌ was: $table->$table->boolean(...)
            $table->boolean('home_rent_module')->default(false);

            $table->boolean('product_module')->default(false);
            $table->boolean('employee_module')->default(false);
            $table->boolean('order_module')->default(false);

            $table->boolean('is_active')->default(true);

            // ❌ was: boolean user_id
            // 👤 User relation (optional)
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modules_history');
    }
};
