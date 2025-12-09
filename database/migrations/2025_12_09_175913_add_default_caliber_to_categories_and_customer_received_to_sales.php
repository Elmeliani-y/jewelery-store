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
        Schema::table('categories', function (Blueprint $table) {
            $table->foreignId('default_caliber_id')->nullable()->after('is_active')->constrained('calibers')->nullOnDelete();
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->boolean('customer_received')->default(false)->after('is_returned');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropForeign(['default_caliber_id']);
            $table->dropColumn('default_caliber_id');
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn('customer_received');
        });
    }
};
