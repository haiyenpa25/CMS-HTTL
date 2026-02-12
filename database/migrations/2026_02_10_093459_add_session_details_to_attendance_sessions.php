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
        Schema::table('attendance_sessions', function (Blueprint $table) {
            $table->string('topic')->nullable()->after('name')->comment('Chủ đề buổi nhóm');
            $table->string('main_scripture')->nullable()->after('topic')->comment('Kinh Thánh chính');
            $table->string('key_verse')->nullable()->after('main_scripture')->comment('Câu gốc');
            // speaker_id already exists from previous migration
            $table->foreignId('mc_id')->nullable()->after('speaker_id')->constrained('members')->onDelete('set null')->comment('Người dẫn chương trình');
            $table->text('notes')->nullable()->after('mc_id')->comment('Ghi chú');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendance_sessions', function (Blueprint $table) {
            $table->dropForeign(['mc_id']);
            $table->dropColumn(['topic', 'main_scripture', 'key_verse', 'mc_id', 'notes']);
        });
    }
};
