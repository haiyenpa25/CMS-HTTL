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
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('family_id')->constrained()->cascadeOnDelete();
            $table->foreignId('title_id')->constrained()->cascadeOnDelete();
            $table->string('full_name');
            $table->string('phone')->nullable();
            $table->string('gender')->nullable();
            $table->date('birthday')->nullable();
            $table->string('blood_group')->nullable();
            $table->string('job')->nullable();
            $table->boolean('is_married')->default(false);
            
            // Spiritual info
            $table->date('date_faith')->nullable();
            $table->date('date_baptism')->nullable();
            $table->date('joined_date')->nullable();
            $table->string('referred_by')->nullable();
            $table->json('spiritual_gifts')->nullable();
            $table->enum('status', ['active', 'inactive', 'weak'])->default('active');
            $table->dateTime('last_visited_at')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
