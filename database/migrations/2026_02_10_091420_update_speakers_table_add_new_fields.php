<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('speakers', function (Blueprint $table) {
            // Add new church_affiliation column
            $table->string('church_affiliation')->nullable()->after('phone');
            
            // Add other new fields
            $table->string('email')->nullable()->after('church_affiliation');
            $table->json('specialties')->nullable()->after('bio')->comment('Chuyên đề giảng dạy');
            $table->enum('status', ['active', 'inactive'])->default('active')->after('specialties');
        });

        // Copy data from organization to church_affiliation
        DB::statement('UPDATE speakers SET church_affiliation = organization');

        // Drop old organization column
        Schema::table('speakers', function (Blueprint $table) {
            $table->dropColumn('organization');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('speakers', function (Blueprint $table) {
            // Re-add organization column
            $table->string('organization')->nullable()->after('phone');
        });

        // Copy data back
        DB::statement('UPDATE speakers SET organization = church_affiliation');

        // Drop new columns
        Schema::table('speakers', function (Blueprint $table) {
            $table->dropColumn(['church_affiliation', 'email', 'specialties', 'status']);
        });
    }
};
