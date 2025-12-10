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
    { Schema::create('modules', function (Blueprint $table) {
          $table->id();
          $table->boolean('company_dashboard_module')->default(false);
            $table->boolean('company_info_module')->default(false);
            $table->boolean('company_branch_module')->default(false);
            $table->boolean('company_category_module')->default(false);
            $table->boolean('company_type_module')->default(false);
            $table->boolean('company_size_module')->default(false);
            $table->boolean('company_offers_type_module')->default(false);
            $table->boolean('company_offers_module')->default(false);
         $table->boolean('product_module')->default(false);
         $table->boolean('employee_module')->default(false);
         $table->boolean('order_module')->default(false);
          $table->boolean('is_active')->default(true);
          $table->boolean('user_id')->nullable();
            $table->timestamps();
        });
        //
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
