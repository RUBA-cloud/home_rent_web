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
        Schema::table('modules', function (Blueprint $table) {
            $table->boolean('order_status_module')->default(false);
            $table->boolean('region_module')->default(false);
            $table->boolean('company_delivery_module',)->default(false);
            $table->boolean('payment_module',)->default(false);
                });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('modules', function (Blueprint $table) {
            $table->dropColumn(['company_offers_type_module', 'company_offers_module']);
        });
    }
};
