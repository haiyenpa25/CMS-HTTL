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
        Schema::table('departments', function (Blueprint $table) {
            $table->enum('department_type', ['chanh_su', 'sinh_hoat', 'muc_vu'])
                  ->default('sinh_hoat')
                  ->after('name')
                  ->comment('Ban Chấp Sự | Ban Sinh Hoạt | Ban Mục vụ');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->dropColumn('department_type');
        });
    }
};
