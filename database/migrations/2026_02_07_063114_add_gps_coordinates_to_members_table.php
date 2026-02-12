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
            $table->decimal('latitude', 10, 8)->nullable()->after('address');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            $table->timestamp('location_updated_at')->nullable()->after('longitude');
            $table->foreignId('location_updated_by')->nullable()->constrained('users')->after('location_updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropForeign(['location_updated_by']);
            $table->dropColumn(['latitude', 'longitude', 'location_updated_at', 'location_updated_by']);
        });
    }
};
