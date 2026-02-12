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
        Schema::table('visits', function (Blueprint $table) {
            $table->foreignId('department_id')->nullable()->after('family_id')->constrained()->onDelete('cascade');
            $table->foreignId('created_by')->nullable()->after('department_id')->constrained('users');
            
            $table->string('visit_type')->default('regular')->after('visit_date'); // sos, suggested, planned, regular
            $table->string('priority')->default('normal')->after('visit_type'); // sos, high, normal, low
            $table->string('status')->default('planned')->after('priority'); // planned, completed, cancelled
            
            $table->text('reason')->nullable()->after('status'); // Lý do thăm
            $table->text('prayer_needs')->nullable()->after('reason'); // Nhu cầu cầu nguyện
            
            // AI Suggest metadata
            $table->integer('weeks_absent')->nullable()->after('prayer_needs');
            $table->integer('months_since_last_visit')->nullable()->after('weeks_absent');
            
            // Rename 'notes' to 'visit_notes' for clarity
            $table->renameColumn('notes', 'visit_notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visits', function (Blueprint $table) {
            $table->renameColumn('visit_notes', 'notes');
            
            $table->dropForeign(['department_id']);
            $table->dropForeign(['created_by']);
            
            $table->dropColumn([
                'department_id', 'created_by', 'visit_type', 'priority', 'status',
                'reason', 'prayer_needs', 'weeks_absent', 'months_since_last_visit'
            ]);
        });
    }
};
