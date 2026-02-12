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
        Schema::table('members', function (Blueprint $table) {
            $table->string('baptized_by')->nullable()->after('date_baptism');
            $table->string('baptism_place')->nullable()->after('baptized_by');
        });

        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->string('type')->default('info'); // info, success, warning, error
            $table->string('message');
            $table->json('payload')->nullable(); // e.g. member_id, etc.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
        
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn(['baptized_by', 'baptism_place']);
        });
    }
};
