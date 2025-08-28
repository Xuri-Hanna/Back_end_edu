<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
      public function up(): void
    {
        Schema::table('phieu_thue_phong', function (Blueprint $table) {
            $table->char('lich_thue', 50)->nullable()->after('den_ngay');
        });
    }

    public function down(): void
    {
        Schema::table('phieu_thue_phong', function (Blueprint $table) {
            $table->dropColumn('lich_thue');
        });
    }
};

