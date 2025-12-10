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
            // Drop foreign key first
            $table->dropForeign(['category_id']);
            
            // Then drop index
            $table->dropIndex(['category_id', 'created_at']);
            
            // Finally drop column
            $table->dropColumn('category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            // Restore category_id column
            $table->foreignId('category_id')->nullable()->constrained('categories');
            $table->index(['category_id', 'created_at']);
        });
    }
};
