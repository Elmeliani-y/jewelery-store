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
            // Drop foreign key first if exists
            if (Schema::hasColumn('sales', 'caliber_id')) {
                $table->dropForeign(['caliber_id']);
                $table->dropIndex(['caliber_id', 'created_at']);
                $table->dropColumn('caliber_id');
            }
            if (Schema::hasColumn('sales', 'weight')) {
                $table->dropColumn('weight');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->foreignId('caliber_id')->nullable()->constrained('calibers');
            $table->index(['caliber_id', 'created_at']);
            $table->decimal('weight', 8, 3)->nullable();
        });
    }
};
