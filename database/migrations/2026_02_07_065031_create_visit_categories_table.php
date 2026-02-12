<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visit_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('color')->default('#3B82F6');
            $table->string('icon')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Seed default categories
        DB::table('visit_categories')->insert([
            ['name' => 'Thăm thường', 'color' => '#3B82F6', 'icon' => '🏠', 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Thăm bệnh', 'color' => '#EF4444', 'icon' => '🏥', 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Thăm mừng', 'color' => '#10B981', 'icon' => '🎉', 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Thăm chia buồn', 'color' => '#6B7280', 'icon' => '🕊️', 'sort_order' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Thăm khuyến khích', 'color' => '#F59E0B', 'icon' => '💪', 'sort_order' => 5, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('visit_categories');
    }
};
