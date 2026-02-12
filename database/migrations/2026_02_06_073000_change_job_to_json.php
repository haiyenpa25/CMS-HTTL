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
            // Change job to json or text to store array. 
            // Using text is safer if database driver has strict json limitations, but json is preferred in Laravel.
            // However, Doctrine/DBAL might need 'json' properly.
            $table->text('job')->change()->nullable(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->string('job')->change()->nullable();
        });
    }
};
