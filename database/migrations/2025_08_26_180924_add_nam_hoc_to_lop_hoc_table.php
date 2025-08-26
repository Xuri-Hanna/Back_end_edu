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
        Schema::table('lop_hoc', function (Blueprint $table) {
              $table->string('nam_hoc')->after('ten_lop');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::table('lop_hoc', function (Blueprint $table) {
            $table->dropColumn('nam_hoc');
        });
    }
};
