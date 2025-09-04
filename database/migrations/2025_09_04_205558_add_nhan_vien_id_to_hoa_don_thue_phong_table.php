<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
     public function up()
    {
        Schema::table('hoa_don_thue_phong', function (Blueprint $table) {
            $table->char('nhan_vien_id',10)->after('id')->nullable();

            $table->foreign('nhan_vien_id')
                  ->references('id')
                  ->on('nhan_vien')
                  ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('hoa_don_thue_phong', function (Blueprint $table) {
            $table->dropForeign(['nhan_vien_id']);
            $table->dropColumn('nhan_vien_id');
        });
    }
};
