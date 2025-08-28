<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
     public function up(): void
    {
        Schema::table('hop_dong_thue_phong', function (Blueprint $table) {
            $table->enum('trang_thai', ['Hết hạn', 'Đã thanh toán', 'Chưa thanh toán'])
                  ->default('Chưa thanh toán')
                  ->after('dieu_khoan'); // đổi "ngay_ket_thuc" thành cột cuối phù hợp trong bảng
        });
    }

    public function down(): void
    {
        Schema::table('hop_dong_thue_phong', function (Blueprint $table) {
            $table->dropColumn('trang_thai');
        });
    }
};
