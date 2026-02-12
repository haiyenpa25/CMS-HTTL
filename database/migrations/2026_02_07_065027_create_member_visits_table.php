<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('member_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->onDelete('cascade');
            $table->foreignId('department_id')->constrained()->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('category_id')->nullable()->constrained('visit_categories')->onDelete('set null');
            
            // Visit scheduling
            $table->date('visit_date');
            $table->date('scheduled_date')->nullable();
            $table->enum('status', ['planned', 'completed', 'cancelled', 'rescheduled'])->default('planned');
            $table->string('visit_type')->default('regular'); // regular, emergency, follow_up
            $table->string('priority')->default('normal'); // high, normal, low
            
            // Visit details
            $table->text('purpose')->nullable();
            $table->text('notes')->nullable();
            $table->text('prayer_requests')->nullable();
            $table->text('outcome')->nullable();
            
            // Tracking
            $table->integer('duration_minutes')->nullable();
            $table->timestamp('completed_at')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for performance
            $table->index(['member_id', 'status']);
            $table->index(['department_id', 'visit_date']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_visits');
    }
};
