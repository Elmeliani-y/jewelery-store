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
        Schema::table('sales', function (Blueprint $table) {
            $table->json('products')->nullable()->after('invoice_number');
            
            // Make existing product-related columns nullable
            $table->foreignId('category_id')->nullable()->change();
            $table->foreignId('caliber_id')->nullable()->change();
            $table->decimal('weight', 8, 3)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn('products');
            
            // Restore original constraints
            $table->foreignId('category_id')->nullable(false)->change();
            $table->foreignId('caliber_id')->nullable(false)->change();
            $table->decimal('weight', 8, 3)->nullable(false)->change();
        });
    }
};
