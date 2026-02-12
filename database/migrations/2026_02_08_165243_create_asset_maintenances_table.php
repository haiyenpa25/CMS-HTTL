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
        Schema::create('asset_maintenances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained('assets')->cascadeOnDelete();
            $table->enum('type', ['Routine', 'Incident', 'Upgrade']);
            $table->text('description');
            $table->decimal('cost', 15, 2)->default(0);
            $table->string('technician_name')->nullable();
            $table->date('scheduled_date')->nullable();
            $table->date('completion_date')->nullable();
            $table->enum('status', ['Pending', 'In Progress', 'Completed', 'Cancelled'])->default('Pending');
            $table->foreignId('reported_by')->nullable()->constrained('users')->comment('User who reported incident');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_maintenances');
    }
};
