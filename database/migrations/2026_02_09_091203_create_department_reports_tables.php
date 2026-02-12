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
        // Main reports table
        Schema::create('department_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained('departments')->onDelete('cascade');
            $table->year('year');
            $table->tinyInteger('month'); // 1-12
            $table->foreignId('created_by')->constrained('users');
            
            // Statistics
            $table->integer('total_attendance')->default(0);
            $table->decimal('total_donations', 12, 2)->default(0);
            $table->integer('visits_completed')->default(0);
            $table->integer('visits_total')->default(0);
            $table->integer('new_members')->default(0);
            
            // Trends
            $table->decimal('attendance_change_percent', 5, 2)->nullable();
            $table->decimal('donations_change_percent', 5, 2)->nullable();
            
            // Comments & Feedback
            $table->text('general_comments')->nullable();
            $table->text('suggestions')->nullable();
            $table->text('prayer_requests')->nullable();
            $table->json('prayer_topics')->nullable();
            
            $table->enum('status', ['draft', 'submitted', 'approved'])->default('draft');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users');
            
            $table->timestamps();
            $table->unique(['department_id', 'year', 'month']);
        });

        // Activities table
        Schema::create('department_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained('department_reports')->onDelete('cascade');
            $table->date('activity_date');
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('donations_received', 12, 2)->default(0);
            $table->integer('attendance')->default(0);
            $table->string('location')->nullable();
            $table->timestamps();
        });

        // Weekly statistics
        Schema::create('department_weekly_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained('department_reports')->onDelete('cascade');
            $table->tinyInteger('week_number'); // 1-4
            $table->integer('attendance')->default(0);
            $table->decimal('donations', 12, 2)->default(0);
            $table->timestamps();
            $table->unique(['report_id', 'week_number']);
        });

        // Monthly comparisons for charts
        Schema::create('department_monthly_comparisons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained('departments')->onDelete('cascade');
            $table->year('year');
            $table->tinyInteger('month');
            $table->integer('attendance')->default(0);
            $table->decimal('donations', 12, 2)->default(0);
            $table->timestamps();
            $table->unique(['department_id', 'year', 'month']);
        });

        // Visit records
        Schema::create('department_visit_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained('department_reports')->onDelete('cascade');
            $table->foreignId('member_id')->constrained('members');
            $table->date('visit_date');
            $table->enum('visit_type', ['sick', 'new_believer', 'follow_up', 'encouragement'])->default('follow_up');
            $table->text('notes')->nullable();
            $table->enum('status', ['completed', 'pending', 'cancelled'])->default('pending');
            $table->timestamps();
        });

        // Next month tasks
        Schema::create('department_next_month_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained('departments')->onDelete('cascade');
            $table->year('year');
            $table->tinyInteger('month');
            $table->tinyInteger('week_number'); // 1-4
            $table->string('task_name');
            $table->text('description')->nullable();
            $table->date('scheduled_date')->nullable();
            $table->string('location')->nullable();
            $table->boolean('is_completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('department_next_month_tasks');
        Schema::dropIfExists('department_visit_records');
        Schema::dropIfExists('department_monthly_comparisons');
        Schema::dropIfExists('department_weekly_stats');
        Schema::dropIfExists('department_activities');
        Schema::dropIfExists('department_reports');
    }
};
