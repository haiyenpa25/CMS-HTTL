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
        Schema::table('assets', function (Blueprint $table) {
            // New identification fields
            $table->string('model')->nullable()->after('name');
            $table->string('manufacturer')->nullable()->after('model'); // was 'brand' in previous, we could rename or add alias, let's add manufacturer and maybe map brand to it later or keep both? Plan says "Manufacturer". Existing table has "brand". Let's stick to "brand" if it exists or use "manufacturer". Let's just add "manufacturer" to be safe and specific.
            $table->string('serial_number')->nullable()->after('code');
            
            // Responsibility fields
            $table->foreignId('managed_by')->nullable()->constrained('users')->comment('Person in charge')->after('description');
            $table->foreignId('used_by_member_id')->nullable()->constrained('members')->comment('Member currently using/borrowing')->after('managed_by');
            
            // Digital profile
            $table->string('manual_url')->nullable()->after('qr_code');
            $table->string('image_url')->nullable()->after('manual_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropForeign(['managed_by']);
            $table->dropForeign(['used_by_member_id']);
            $table->dropColumn([
                'model', 
                'manufacturer', 
                'serial_number', 
                'managed_by', 
                'used_by_member_id', 
                'manual_url', 
                'image_url'
            ]);
        });
    }
};
