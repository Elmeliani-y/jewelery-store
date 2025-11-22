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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches');
            $table->foreignId('expense_type_id')->constrained('expense_types');
            $table->string('description'); // بيان أو غرض المصروف
            $table->decimal('amount', 10, 2); // المبلغ
            $table->date('expense_date');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['branch_id', 'expense_date']);
            $table->index(['expense_type_id', 'expense_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};