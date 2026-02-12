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
        Schema::create('positions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Trưởng ban, Thư ký, Thủ quỹ, Giáo viên...');
            $table->string('slug')->unique()->comment('truong-ban, thu-ky...');
            $table->text('description')->nullable();
            $table->integer('level')->default(0)->comment('Hierarchy level for ordering');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('positions');
    }
};
