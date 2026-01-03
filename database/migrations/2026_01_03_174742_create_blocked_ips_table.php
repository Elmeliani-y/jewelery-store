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
        Schema::create('blocked_ips', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address', 45)->unique(); // IPv4 or IPv6
            $table->integer('failed_attempts')->default(0);
            $table->timestamp('blocked_at')->nullable();
            $table->timestamp('last_attempt_at')->nullable();
            $table->timestamps();
            
            $table->index('ip_address');
            $table->index('blocked_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blocked_ips');
    }
};
