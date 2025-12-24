<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        if (!Schema::hasColumn('devices', 'user_id')) {
            Schema::table('devices', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id')->nullable();
            });
        }
    }
    public function down()
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });
    }
};
