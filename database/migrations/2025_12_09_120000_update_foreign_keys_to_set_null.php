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
        // Update sales table foreign keys
        Schema::table('sales', function (Blueprint $table) {
            // Drop existing foreign keys
            $table->dropForeign(['branch_id']);
            $table->dropForeign(['employee_id']);
            
            // Make columns nullable
            $table->foreignId('branch_id')->nullable()->change();
            $table->foreignId('employee_id')->nullable()->change();
            
            // Re-add foreign keys with SET NULL on delete
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('set null');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('set null');
        });

        // Update expenses table foreign keys
        Schema::table('expenses', function (Blueprint $table) {
            // Drop existing foreign key
            $table->dropForeign(['branch_id']);
            
            // Make column nullable
            $table->foreignId('branch_id')->nullable()->change();
            
            // Re-add foreign key with SET NULL on delete
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert sales table
        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropForeign(['employee_id']);
            
            $table->foreignId('branch_id')->nullable(false)->change();
            $table->foreignId('employee_id')->nullable(false)->change();
            
            $table->foreign('branch_id')->references('id')->on('branches');
            $table->foreign('employee_id')->references('id')->on('employees');
        });

        // Revert expenses table
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            
            $table->foreignId('branch_id')->nullable(false)->change();
            
            $table->foreign('branch_id')->references('id')->on('branches');
        });
    }
};
