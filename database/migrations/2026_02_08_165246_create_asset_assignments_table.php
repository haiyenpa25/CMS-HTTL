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
        Schema::create('asset_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained('assets');
            $table->foreignId('user_id')->nullable()->constrained('users')->comment('Assigned to specific user');
            $table->foreignId('department_id')->constrained('departments')->comment('Assigned to department');
            $table->date('assigned_date');
            $table->date('return_date_expected')->nullable();
            $table->date('return_date_actual')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['Active', 'Returned'])->default('Active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_assignments');
    }
};
