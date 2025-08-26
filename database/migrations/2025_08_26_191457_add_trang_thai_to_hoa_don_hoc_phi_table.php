<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('hoa_don_hoc_phi', function (Blueprint $table) {
            $table->enum('trang_thai', ['Chưa thanh toán', 'Đã thanh toán'])
                ->default('Chưa thanh toán')
                ->after('tong_tien'); // đặt sau cột tổng tiền (hoặc cột bạn muốn)
        });
    }

    public function down()
    {
        Schema::table('hoa_don_hoc_phi', function (Blueprint $table) {
            $table->dropColumn('trang_thai');
        });
    }
};
