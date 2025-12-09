<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $caliber21 = DB::table('calibers')->where('name', '21')->first();
        $caliber22 = DB::table('calibers')->where('name', '22')->first();
        $caliber24 = DB::table('calibers')->where('name', '24')->first();

        if ($caliber21) {
            DB::table('categories')->whereIn('name', [
                'غوايش', 'دبلة', 'خاتم', 'سلسلة', 'تعليقة', 'كف', 
                'سبحة', 'سوارة', 'عقد', 'خلخال', 'تشكيلة', 'طقم', 'نص طقم', 'حلق'
            ])->update(['default_caliber_id' => $caliber21->id]);
        }

        if ($caliber24) {
            DB::table('categories')->where('name', 'سبايك')
                ->update(['default_caliber_id' => $caliber24->id]);
        }

        if ($caliber22) {
            DB::table('categories')->where('name', 'جنية')
                ->update(['default_caliber_id' => $caliber22->id]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('categories')->update(['default_caliber_id' => null]);
    }
};
