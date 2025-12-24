<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        if (Schema::hasColumn('devices', 'admin_id')) {
            Schema::table('devices', function (Blueprint $table) {
                $table->dropColumn('admin_id');
            });
        }
    }
    public function down()
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->unsignedBigInteger('admin_id')->nullable();
        });
    }
};
