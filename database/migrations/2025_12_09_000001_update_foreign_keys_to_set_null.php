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
        // Update sales table to set null on branch/employee deletion
        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropForeign(['employee_id']);
        });
        
        Schema::table('sales', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->change();
            $table->foreignId('employee_id')->nullable()->change();
            
            $table->foreign('branch_id')
                ->references('id')
                ->on('branches')
                ->onDelete('set null');
            
            $table->foreign('employee_id')
                ->references('id')
                ->on('employees')
                ->onDelete('set null');
        });

        // Update expenses table to set null on branch deletion
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
        });
        
        Schema::table('expenses', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->change();
            
            $table->foreign('branch_id')
                ->references('id')
                ->on('branches')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to cascade deletion
        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropForeign(['employee_id']);
        });
        
        Schema::table('sales', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable(false)->change();
            $table->foreignId('employee_id')->nullable(false)->change();
            
            $table->foreign('branch_id')
                ->references('id')
                ->on('branches')
                ->onDelete('cascade');
            
            $table->foreign('employee_id')
                ->references('id')
                ->on('employees')
                ->onDelete('cascade');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
        });
        
        Schema::table('expenses', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable(false)->change();
            
            $table->foreign('branch_id')
                ->references('id')
                ->on('branches')
                ->onDelete('cascade');
        });
    }
};
