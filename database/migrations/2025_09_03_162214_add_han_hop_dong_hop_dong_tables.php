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
        Schema::table('hop_dong_thue_phong', function (Blueprint $table) {
             $table->enum('han_hop_dong', ['Chờ', 'Còn thời hạn','Kết thúc','Hủy'])
                  ->default('Chờ')
                  ->after('ngay_lap');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hop_dong_thue_phong', function (Blueprint $table) {
              $table->dropColumn('han_hop_dong');
        });
    }
};
