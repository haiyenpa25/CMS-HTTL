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
        // 1. Update Users Table
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('member_id')->nullable()->constrained()->onDelete('set null');
            $table->string('role')->default('user'); // admin, secretary, user
        });

        // 2. Attendance Sessions (Buổi điểm danh)
        Schema::create('attendance_sessions', function (Blueprint $table) {
            $table->id();
            $table->date('date'); // Buổi nhóm ngày nào
            $table->string('type')->default('sunday_service'); // sunday_service, department_meeting
            $table->string('name')->nullable(); // Tên buổi nhóm (VD: Thờ phượng Chúa nhật 06/02)
            $table->string('status')->default('open'); // open, closed, locked
            $table->string('access_scope')->default('global'); // global (all depts), department (specific dept)
            
            // For Secretary Validation
            $table->integer('manual_count')->nullable(); // Số đếm thực tế (ngồi đếm tay)
            $table->text('note')->nullable();
            
            $table->timestamps();
        });

        // 3. Attendances (Điểm danh chi tiết từng người)
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_session_id')->constrained()->onDelete('cascade');
            $table->foreignId('member_id')->constrained()->onDelete('cascade');
            
            // Snapshot of where they were checked in (Role/Group might change later)
            $table->foreignId('department_id')->nullable()->constrained()->onDelete('set null'); 
            $table->foreignId('sub_group_id')->nullable()->constrained()->onDelete('set null');

            // Tracking Data
            $table->boolean('is_present')->default(false);
            $table->boolean('memorized_scripture')->default(false); // Thuộc câu gốc
            $table->integer('bible_answers_count')->default(0); // Số câu trả lời KT (nếu có thi đua)
            
            $table->timestamps();

            // Prevent duplicate check-in for same person in same session
            $table->unique(['attendance_session_id', 'member_id']);
        });

        // 4. Attendance Summaries (Số liệu tổng cho phương án "Nhập nhanh")
        Schema::create('attendance_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_session_id')->constrained()->onDelete('cascade');
            $table->foreignId('department_id')->constrained()->onDelete('cascade');
            
            $table->integer('total_present')->default(0);
            $table->boolean('is_manual_entry')->default(true); // True if entered via "Quick Add", False if calculated from attendances
            
            $table->timestamps();
            
            $table->unique(['attendance_session_id', 'department_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_summaries');
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('attendance_sessions');
        
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['member_id']);
            $table->dropColumn(['member_id', 'role']);
        });
    }
};
