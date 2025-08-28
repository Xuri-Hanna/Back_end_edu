<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::table('hoa_don_thue_phong', function (Blueprint $table) {
        // Xóa ràng buộc trước khi xóa cột
        if (Schema::hasColumn('hoa_don_thue_phong', 'phieu_thue_id')) {
            $table->dropForeign(['phieu_thue_id']); // bỏ foreign key
            $table->dropColumn('phieu_thue_id');    // xóa cột
        }

        if (Schema::hasColumn('hoa_don_thue_phong', 'ngay_het_han')) {
            $table->dropColumn('ngay_het_han');
        }

        // Thêm cột mới
        $table->string('hop_dong_id', 50);
        $table->date('ngay_lap')->nullable();

        // Khóa ngoại nối với hop_dong_thue_phong
        $table->foreign('hop_dong_id')
            ->references('id')
            ->on('hop_dong_thue_phong')
            ->onDelete('cascade');
    });
}

    public function down(): void
    {
        Schema::table('hoa_don_thue_phong', function (Blueprint $table) {
            // Rollback: thêm lại cột cũ
            $table->string('phieu_thue_id', 50);
            $table->date('ngay_het_han')->nullable();

            $table->dropForeign(['hop_dong_id']);
            $table->dropColumn(['hop_dong_id', 'ngay_lap']);
        });
    }
};
