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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique()->comment('TBT-001, MT-002');
            $table->string('name');
            $table->foreignId('category_id')->constrained('asset_categories')->cascadeOnDelete();
            $table->string('brand')->nullable();
            $table->date('purchase_date')->nullable();
            $table->date('warranty_expiry')->nullable();
            $table->decimal('price', 15, 2)->nullable();
            $table->decimal('current_value', 15, 2)->nullable();
            $table->enum('status', ['Active', 'Repairing', 'Broken', 'Lost', 'Disposed'])->default('Active');
            $table->foreignId('location_id')->nullable()->constrained('departments')->comment('Current location');
            $table->text('description')->nullable();
            $table->string('qr_code')->nullable()->comment('QR code image path');
            $table->date('next_maintenance_date')->nullable();
            $table->foreignId('replaced_by_asset_id')->nullable()->constrained('assets')->comment('Replacement tracking');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
