<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        if (!Schema::hasColumn('devices', 'last_login_at')) {
            Schema::table('devices', function (Blueprint $table) {
                $table->timestamp('last_login_at')->nullable()->after('user_agent');
            });
        }
    }
    public function down()
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->dropColumn('last_login_at');
        });
    }
};
