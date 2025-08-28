<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
    {
        // Tạo bảng công nợ
        Schema::create('cong_no', function (Blueprint $table) {
            $table->id();
            $table->decimal('tien_no', 15, 2)->default(0);
            $table->decimal('da_tra', 15, 2)->default(0);
            $table->timestamps();
        });

        // Thêm cột cong_no_id vào bảng hop_dong_thue_phong
        Schema::table('hop_dong_thue_phong', function (Blueprint $table) {
            $table->unsignedBigInteger('cong_no_id')->nullable()->after('thanh_tien');

            $table->foreign('cong_no_id')
                  ->references('id')->on('cong_no')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('hop_dong_thue_phong', function (Blueprint $table) {
            $table->dropForeign(['cong_no_id']);
            $table->dropColumn('cong_no_id');
        });

        Schema::dropIfExists('cong_no');
    }
};
