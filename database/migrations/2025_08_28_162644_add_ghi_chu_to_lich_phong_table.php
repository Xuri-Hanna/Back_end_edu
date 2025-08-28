<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
     public function up(): void
    {
        Schema::table('lich_phong', function (Blueprint $table) {
            $table->text('ghi_chu')->nullable()->after('trang_thai');
        });
    }

    public function down(): void
    {
        Schema::table('lich_phong', function (Blueprint $table) {
            $table->dropColumn('ghi_chu');
        });
    }
};
