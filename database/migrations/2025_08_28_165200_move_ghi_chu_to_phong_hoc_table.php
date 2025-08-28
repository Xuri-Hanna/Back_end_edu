<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Thêm cột ghi_chu vào bảng phong_hoc
        Schema::table('phong_hoc', function (Blueprint $table) {
            $table->text('ghi_chu')->nullable();
        });

        // Xóa cột ghi_chu khỏi bảng lich_phong
        Schema::table('lich_phong', function (Blueprint $table) {
            $table->dropColumn('ghi_chu');
        });
    }

    public function down(): void
    {
        Schema::table('phong_hoc', function (Blueprint $table) {
            $table->dropColumn('ghi_chu');
        });

        Schema::table('lich_phong', function (Blueprint $table) {
            $table->text('ghi_chu')->nullable();
        });
    }
};
