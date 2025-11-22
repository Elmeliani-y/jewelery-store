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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('branch_id')->constrained('branches');
            $table->foreignId('employee_id')->constrained('employees');
            $table->foreignId('category_id')->constrained('categories');
            $table->foreignId('caliber_id')->constrained('calibers');
            $table->decimal('weight', 8, 3); // الوزن بالجرام
            $table->decimal('total_amount', 10, 2); // إجمالي المبلغ
            $table->decimal('cash_amount', 10, 2)->default(0); // المبلغ النقدي
            $table->decimal('network_amount', 10, 2)->default(0); // مبلغ الشبكة
            $table->string('network_reference')->nullable(); // رقم المعاملة للشبكة
            $table->enum('payment_method', ['cash', 'network', 'mixed']); // طريقة الدفع
            $table->decimal('tax_amount', 10, 2)->default(0); // مبلغ الضريبة
            $table->decimal('net_amount', 10, 2); // المبلغ الصافي بدون ضريبة
            $table->text('notes')->nullable();
            $table->boolean('is_returned')->default(false); // هل تم استرجاع الفاتورة
            $table->timestamp('returned_at')->nullable();
            $table->timestamps();

            $table->index(['branch_id', 'created_at']);
            $table->index(['employee_id', 'created_at']);
            $table->index(['category_id', 'created_at']);
            $table->index(['caliber_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};