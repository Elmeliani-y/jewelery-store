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
        Schema::create('calibers', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // 24, 22, 21, 18
            $table->decimal('tax_rate', 5, 2)->default(0); // معدل الضريبة لكل عيار
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calibers');
    }
};