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
        Schema::create('asset_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique()->comment('Ticket ID, e.g. TK-2024-001');
            $table->foreignId('asset_id')->constrained('assets')->cascadeOnDelete();
            $table->enum('type', ['repair', 'maintenance', 'inspection', 'other']);
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->enum('status', ['new', 'approved', 'in_progress', 'completed', 'cancelled', 'rejected'])->default('new');
            
            $table->text('issue_description');
            $table->json('images')->nullable()->comment('Evidence photos');
            $table->text('technician_notes')->nullable();
            
            $table->decimal('cost', 15, 2)->default(0);
            
            $table->foreignId('reported_by')->constrained('users');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->comment('Technician or admin in charge');
            
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_tickets');
    }
};
