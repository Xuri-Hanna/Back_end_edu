<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
     public function up(): void
    {
        Schema::table('phieu_thue_phong', function (Blueprint $table) {
            $table->date('ngay_lap')->nullable()->after('trang_thai');
        });

        Schema::table('hop_dong_thue_phong', function (Blueprint $table) {
            $table->date('ngay_lap')->nullable()->after('cong_no_id');
        });
    }

    public function down(): void
    {
        Schema::table('phieu_thue_phong', function (Blueprint $table) {
            $table->dropColumn('ngay_lap');
        });

        Schema::table('hop_dong_thue_phong', function (Blueprint $table) {
            $table->dropColumn('ngay_lap');
        });
    }
};
