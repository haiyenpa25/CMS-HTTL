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
        Schema::create('session_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('attendance_sessions')->onDelete('cascade');
            $table->foreignId('member_id')->constrained('members')->onDelete('cascade');
            $table->string('role_name')->comment('Vị trí phân công: Đàn Piano, Tiếp tân, Kỹ thuật...');
            $table->text('note')->nullable();
            $table->timestamps();
            
            // Prevent duplicate assignments for same role
            $table->unique(['session_id', 'member_id', 'role_name'], 'unique_session_member_role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('session_assignments');
    }
};
