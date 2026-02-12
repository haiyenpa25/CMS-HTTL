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
        Schema::table('user_assignments', function (Blueprint $table) {
            $table->foreignId('position_id')
                  ->nullable()
                  ->after('sub_group_id')
                  ->constrained('positions')
                  ->nullOnDelete()
                  ->comment('Chức vụ trong ban: Trưởng ban, Thư ký...');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_assignments', function (Blueprint $table) {
            $table->dropForeign(['position_id']);
            $table->dropColumn('position_id');
        });
    }
};
